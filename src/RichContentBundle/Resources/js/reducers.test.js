import reducer from './reducers';

describe('CONTENT_SAVE', () => {
  it('sets content id of successful response', () => {
    expect(reducer({}, {
      type: 'CONTENT_SAVE',
      status: true,
      json: {
        id: 1
      }
    })).toEqual({
      contentId: 1,
    });
  });
});

describe('CONTENT_LOAD', () => {
  it('does nothing with no content id', () => {
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
