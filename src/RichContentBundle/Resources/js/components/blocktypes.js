import Text from './Text';
import Image from './Image';

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
  }
}
