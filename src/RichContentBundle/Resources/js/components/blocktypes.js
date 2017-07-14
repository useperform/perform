import Text from './Text';
import Image from './Image';
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
