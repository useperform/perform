import Text from './types/Text';
import Image from './types/Image';
import Quote from './types/Quote';

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
  }
}
