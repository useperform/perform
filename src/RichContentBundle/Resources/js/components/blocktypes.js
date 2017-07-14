import Text from './types/Text';
import Image from './types/Image';
import Quote from './types/Quote';
import Video from './types/Video';

export default {
  text: {
    class: Text,
    defaults: {
      content: 'Text content',
    },
  },
  image: {
    class: Image,
    defaults: {
      src: '/favicon.ico',
    }
  },
  quote: {
    class: Quote,
    defaults: {
      text: '',
      cite: '',
    }
  },
  video: {
    class: Video,
    defaults: {
    }
  }
}
