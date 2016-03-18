import { Component, Element, B } from '/components/component.js'
import gallery from 'react-photo-gallery'
import $ from 'jquery'
import style from './style.css'

const Gallery = Element(gallery)


const flickr = 'https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=372ef3a005d9b9df062b8240c326254d&photoset_id=72157631971715898&user_id=57933175@N08&format=json&per_page=6&page=4&extras=url_o,url_m,url_l,url_c'


const fmtImage = (obj, i) => {
  let aspectRatio = parseFloat(obj.width_o / obj.height_o)

  return {
    src: (aspectRatio >= 3) ? obj.url_c : obj.url_m,
    width: parseInt(obj.width_o),
    height: parseInt(obj.height_o),
    aspectRatio: aspectRatio,
    lightboxImage:{ src: obj.url_l, caption: obj.title },
  }
}


export default Component({
  getInitialState () {
    return { photos: null }
  },
  componentDidMount () {
    $.ajax({
      url: flickr,
      dataType: 'jsonp',
      jsonpCallback: 'jsonFlickrApi',
      cache: false,
      success: (data) => {
        this.setState({ photos: data.photoset.photo.map(fmtImage) })
      }
    })
  },
  render () {
    const { photos } = this.state

    if (!photos) return B({ className: style.loading }, 'Loading gallery ...')

    return B({ className: style.gallery }, Gallery({ photos }))
  }
})


