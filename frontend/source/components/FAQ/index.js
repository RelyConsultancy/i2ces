import { Component, B, HTML } from '/components/component.js'
import Froala from '/components/Froala'
import { fetchFAQ, saveFAQ, isI2C } from '/application/actions.js'
import store from '/application/store.js'
import style from './style.css'


export default Component({
  class: true,
  editor () {
    const { editMode, content } = this.state

    const editor = Froala({
      content,
      options: {
        imageUploadParam: 'image',
        // imageUploadURL: uploadPath,
      },
      onChange: (e, editor) => {
        this.content = editor.html.get()
      },
    })

    return editor
  },
  getInitialState () {
    return {
      content: '',
      editMode: false,
    }
  },
  componentDidMount () {
    fetchFAQ((data) => {
      this.setState({ content: data.content })
    })
  },
  render () {
    const { editMode, content } = this.state

    const toggle = !isI2C() ? null: B({
      className: style.toggle,
      onClick: () => {
        this.setState({ editMode: !editMode })

        if (editMode) {
          this.setState({ content: this.content })
        }
      }
    }, editMode ? 'Save' : 'Edit')

    const header = B({ className: style.header }, 'FAQs', toggle)

    return B({ className: style.faq }, header, B(
      { className: style.content },
      editMode ? this.editor() : HTML(content)
    ))
  }
})