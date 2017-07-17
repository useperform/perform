import Text from './types/Text';
import Image from './types/Image';
import Quote from './types/Quote';
import Video from './types/Video';

export default {
  text: {
    class: Text,
    name: 'Text',
    description: 'Words and paragraphs.',
    defaults: {
      content: 'Text content',
    },
  },
  image: {
    class: Image,
    name: 'Image',
    description: 'Images from the media library.',
    defaults: {
      src: '/favicon.ico',
    }
  },
  quote: {
    class: Quote,
    name: 'Quote',
    description: 'Prominently display a quote.',
    defaults: {
      text: '',
      cite: '',
    }
  },
  video: {
    class: Video,
    name: 'Video',
    description: 'Embed a video from youtube, vimeo, or the media library.',
    defaults: {
    }
  }
}
