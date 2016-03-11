import $ from './jquery-2.2.1.js'
import Froala from './js/froala_editor.min.js'
import align from './js/plugins/align.min.js'
import colors from './js/plugins/colors.min.js'
import font_family from './js/plugins/font_family.min.js'
import font_size from './js/plugins/font_size.min.js'
import fullscreen from './js/plugins/fullscreen.min.js'
import image from './js/plugins/image.min.js'
import image_manager from './js/plugins/image_manager.min.js'
import line_breaker from './js/plugins/line_breaker.min.js'
import link from './js/plugins/link.min.js'
import lists from './js/plugins/lists.min.js'
import paragraph_format from './js/plugins/paragraph_format.min.js'
import paragraph_style from './js/plugins/paragraph_style.min.js'
import save from './js/plugins/save.min.js'
import table from './js/plugins/table.min.js'
import url from './js/plugins/url.min.js'
import video from './js/plugins/video.min.js'
// import char_counter from './js/plugins/char_counter.min.js'
// import code_beautifier from './js/plugins/code_beautifier.min.js'
// import code_view from './js/plugins/code_view.min.js'
// import draggable from './js/plugins/draggable.min.js'
// import emoticons from './js/plugins/emoticons.min.js'
// import entities from './js/plugins/entities.min.js'
// import file from './js/plugins/file.min.js'
// import forms from './js/plugins/forms.min.js'
// import inline_style from './js/plugins/inline_style.min.js'
// import quick_insert from './js/plugins/quick_insert.min.js'
// import quote from './js/plugins/quote.min.js'


// Insert icons stylesheet
$('head').append('<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">')

// initiate Froala, it sets itself to jQuery
const froala = Froala()

// initiate plugins
align()
colors()
font_family()
font_size()
fullscreen()
image()
image_manager()
line_breaker()
link()
lists()
paragraph_format()
paragraph_style()
save()
table()
url()
video()
// char_counter()
// code_beautifier()
// code_view()
// draggable()
// emoticons()
// entities()
// file()
// forms()
// inline_style()
// quick_insert()
// quote()


export default froala