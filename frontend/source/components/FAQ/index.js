import { Component, B, Input, HTML } from '/components/component.js'
import Froala from '/components/Froala'
import { fetchFAQ, saveFAQ, isI2C } from '/application/actions.js'
import store from '/application/store.js'
import style from './style.css'


export default Component({
  class: true,
  toSave: null,
  toggleEditMode () {
    const { editMode } = this.state
    const update = { editMode: !editMode }

    if (editMode && this.toSave) {
      update.content = this.toSave

      saveFAQ({
        title: this.state.title,
        content: update.content,
      })
    }

    this.setState(update)
  },
  getInitialState () {
    return {
      content: '',
      title: '',
      editMode: false,
    }
  },
  componentDidMount () {
    fetchFAQ(data => this.setState(data))
  },
  render () {
    const { editMode, title, content } = this.state

    if (isI2C()) {
      var toggle = B({
        className: style.toggle,
        onClick: this.toggleEditMode,
      }, editMode ? 'Save' : 'Edit')
    }

    if (editMode) {
      var input = Input({
        type: 'text',
        value: title,
        onChange: (e) => {
          this.setState({ title: e.target.value })
        }
      })

      var editor = Froala({
        content,
        options: {
          imageUploadParam: 'image',
          imageUploadURL: '/api/images/page/faq',
        },
        onChange: (e, editor) => {
          this.toSave = editor.html.get()
        },
      })
    }

    return B(
      { className: style.faq },
      B({ className: style.header }, input || title, toggle),
      B({ className: style.content }, editor || HTML(content))
    )
  }
})