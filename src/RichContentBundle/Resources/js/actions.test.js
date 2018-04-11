import {getPostBody} from './actions';

describe('getPostBody', () => {
  it('only returns blocks for the named editor', () => {
    const state = {
      blocks: {
        'some-guid-1': {
          type: 'text',
          value: 'block1'
        },
        'some-guid-2': {
          type: 'text',
          value: 'block2'
        },
        '_new1': {
          type: 'text',
          value: 'new1'
        },
        '_new2': {
          type: 'text',
          value: 'new2'
        },
      },
      editors: [
        {
          contentId: 1,
          order: [
            ['some-guid-1', 'some-react-key-2j37'],
            ['_new1', 'some-react-key-jf84'],
            ['_new2', 'some-react-key-73ux'],
          ]
        },
        {
          contentId: 2,
          order: [
            ['some-guid-2', 'some-react-key-2j37'],
            ['_new2', 'some-react-key-73ux'],
          ]
        }
      ]
    }

    const firstEditor = getPostBody(state, 0);
    expect(firstEditor.blocks).toEqual({
      'some-guid-1': {
        type: 'text',
        value: 'block1'
      },
    });
    expect(firstEditor.newBlocks).toEqual({
      '_new1': {
        type: 'text',
        value: 'new1'
      },
      '_new2': {
        type: 'text',
        value: 'new2'
      },
    });
    expect(firstEditor.order).toEqual([
      'some-guid-1',
      '_new1',
      '_new2',
    ]);

    const secondEditor = getPostBody(state, 1);
    expect(secondEditor.blocks).toEqual({
      'some-guid-2': {
        type: 'text',
        value: 'block2'
      },
    });
    expect(secondEditor.newBlocks).toEqual({
      '_new2': {
        type: 'text',
        value: 'new2'
      },
    });
    expect(secondEditor.order).toEqual([
      'some-guid-2',
      '_new2',
    ]);
  });
});
