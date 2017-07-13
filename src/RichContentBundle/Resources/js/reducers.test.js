import reducer from './reducers';

describe('CONTENT_SAVE', () => {
  it('sets content id on success', () => {
    const initialState = {
      order: []
    }
    expect(reducer(initialState, {
      type: 'CONTENT_SAVE',
      status: true,
      json: {
        id: 1,
        newBlocks: {},
      }
    }).contentId).toEqual(1);
  });

  it('updates the ids of new blocks on success', () => {
    const initialState = {
      contentId: 1,
      blocks: {
        'some-guid-1': {
          type: 'text',
          value: 'block1'
        }
      },
      newBlocks: {
        '_stub1': {
          type: 'text',
          value: 'new1'
        },
        '_stub2': {
          type: 'text',
          value: 'new2'
        },
      },
      order: [
        ['_stub1', 'some-react-key-jf84'],
        ['some-guid-1', 'some-react-key-2j37'],
        ['_stub2', 'some-react-key-73ux'],
      ]
    }
    const newState = {
      contentId: 1,
      blocks: {
        'some-guid-1': {
          type: 'text',
          value: 'block1'
        },
        'some-guid-2': {
          type: 'text',
          value: 'new1'
        },
        'some-guid-3': {
          type: 'text',
          value: 'new2'
        },
      },
      newBlocks: {},
      order: [
        ['some-guid-2', 'some-react-key-jf84'],
        ['some-guid-1', 'some-react-key-2j37'],
        ['some-guid-3', 'some-react-key-73ux'],
      ]
    }

    expect(reducer(initialState, {
      type: 'CONTENT_SAVE',
      status: true,
      json: {
        id: 1,
        newBlocks: {
          '_stub1': 'some-guid-2',
          '_stub2': 'some-guid-3',
        }
      }
    })).toEqual(newState);
  });
});

describe('CONTENT_LOAD', () => {
  it('does nothing without a content id', () => {
    expect(reducer({foo: 'bar'}, {
      type: 'CONTENT_LOAD',
    })).toEqual({
      foo: 'bar'
    });
  });

  it('assigns data from a successful response', () => {
    const blocks = {
      'some-guid': {
        type: 'text',
        value: 'block-value',
      }
    };
    const order = ['some-guid'];
    const result = reducer({}, {
      type: 'CONTENT_LOAD',
      id: 1,
      status: true,
      json: {
        blocks,
        order
      }
    })

    expect(result.blocks).toEqual(blocks);
    expect(result.order.length).toBe(1);
    expect(result.order[0].length).toBe(2);
    expect(result.order[0][0]).toBe('some-guid');
  });
});
