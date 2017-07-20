import reducer from './reducers';

import {addBlock, removeBlock, moveBlock} from './actions';
import deepFreeze from 'deep-freeze';

const initialState = deepFreeze({
  blocks: {},
  editors: [],
});

const initialStateWithEditor = deepFreeze({
  blocks: {},
  editors: [{
    contentId: false,
    loaded: false,
    order: [],
  }],
});

describe('CONTENT_SAVE', () => {
  it('sets content id on success', () => {
    const result = reducer(initialStateWithEditor, {
      type: 'CONTENT_SAVE',
      status: true,
      editorIndex: 0,
      json: {
        id: 1,
        newBlocks: {},
      }
    });
    expect(result.editors[0].contentId).toEqual(1);
  });

  it('updates the ids of new blocks on success', () => {
    const initialState = deepFreeze({
      blocks: {
        'some-guid-1': {
          type: 'text',
          value: 'block1'
        },
        '_stub1': {
          type: 'text',
          value: 'new1'
        },
        '_stub2': {
          type: 'text',
          value: 'new2'
        },
      },
      editors: [{
        contentId: 1,
        order: [
          ['_stub1', 'some-react-key-jf84'],
          ['some-guid-1', 'some-react-key-2j37'],
          ['_stub2', 'some-react-key-73ux'],
        ]
      }]
    });
    const newState = {
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
      editors: [{
        contentId: 1,
        order: [
          ['some-guid-2', 'some-react-key-jf84'],
          ['some-guid-1', 'some-react-key-2j37'],
          ['some-guid-3', 'some-react-key-73ux'],
        ]
      }]
    }

    expect(reducer(initialState, {
      type: 'CONTENT_SAVE',
      status: true,
      editorIndex: 0,
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
      editorIndex: 0,
      status: true,
      json: {
        blocks,
        order
      }
    })

    expect(result.blocks).toEqual(blocks);
    expect(result.editors[0].order.length).toBe(1);
    expect(result.editors[0].order[0].length).toBe(2);
    expect(result.editors[0].order[0][0]).toBe('some-guid');
  });

  it('doesn\'t remove existing blocks', () => {
    const initialState = {
      blocks: {
        'some-guid': {
          type: 'text',
          value: 'block-value',
        }
      }
    };
    const result = reducer(initialState, {
      type: 'CONTENT_LOAD',
      id: 1,
      editorIndex: 0,
      status: true,
      json: {
        blocks: {
          'some-new-guid': {
            type: 'text',
            value: 'new-block-value',
          },
        },
        order: [],
      }
    });

    expect(result.blocks['some-guid'].type).toEqual('text');
  });
});

describe('BLOCK_ADD', () => {
  it('creates a new block with the supplied type and value', () => {
    const result = reducer(initialStateWithEditor, addBlock('text', 0));

    const blockKeys = Object.keys(result.blocks);
    expect(blockKeys.length).toEqual(1);
    expect(blockKeys[0].substring(0, 1)).toEqual('_');
    expect(result.blocks[blockKeys[0]].type).toEqual('text');
  });

  it('adds a new block to the order', () => {
    const result = reducer(initialStateWithEditor, addBlock('text', 0));

    expect(result.editors[0].order.length).toEqual(1);
    expect(result.editors[0].order[0].length).toEqual(2);
    expect(result.editors[0].order[0][0].substring(0, 1)).toEqual('_');
  });
});

describe('BLOCK_REMOVE', () => {
  it('removes a block from the order', () => {
    const withBlock = deepFreeze(reducer(initialStateWithEditor, addBlock('text', 0)));
    const result = reducer(withBlock, removeBlock([0, 0]));

    expect(result.editors[0].order.length).toEqual(0);
    expect(Object.keys(result.blocks).length).toEqual(0);
  });

  it('keeps a block reference if it\'s used elsewhere', () => {
    const withBlock = deepFreeze(reducer(initialStateWithEditor, addBlock('text', 0)));
    const withTwoEditors = deepFreeze(Object.assign({}, withBlock, {
      editors: [
        withBlock.editors[0],
        {
          contentId: false,
          loaded: false,
          order: withBlock.editors[0].order,
        }
      ]
    }))
    const result = reducer(withTwoEditors, removeBlock([0, 0]));

    expect(result.editors[0].order.length).toEqual(0);
    expect(Object.keys(result.blocks).length).toEqual(1);
  });
});

describe('BLOCK_MOVE', () => {
  it('moves a block into a new position within the same editor', () => {
    const initialOrder = [
      ['some-guid-1', 'some-react-key-0000'],
      ['some-guid-1', 'some-react-key-1111'],
      ['some-guid-2', 'some-react-key-2222'],
      ['some-guid-2', 'some-react-key-3333'],
    ];
    const initialState = deepFreeze({
      blocks: {
        'some-guid-1': {
          type: 'text',
          value: 'block1'
        },
        'some-guid-2': {
          type: 'text',
          value: 'block2'
        },
      },
      editors: [{
        order: initialOrder
      }]
    });

    const tests = [
      {
        from: 1,
        to: 2,
        order: [0, 2, 1, 3],
      },
      {
        from: 2,
        to: 1,
        order: [0, 2, 1, 3],
      },
      {
        from: 0,
        to: 3,
        order: [1, 2, 3, 0],
      },
      {
        from: 3,
        to: 0,
        order: [3, 0, 1, 2],
      },
      {
        from: 2,
        to: 3,
        order: [0, 1, 3, 2],
      },
      {
        from: 0,
        to: -1,
        order: [0, 1, 2, 3],
      },
      {
        from: 2,
        to: 5,
        order: [0, 1, 3, 2],
      },
      {
        from: 3,
        to: 5,
        order: [0, 1, 2, 3],
      }
    ];

    for (let i=0; i < tests.length; i++) {
      let expectedOrder = [];
      for (let k=0; k < tests[i].order.length; k++) {
        expectedOrder.push(initialOrder[tests[i].order[k]]);
      }
      let result = reducer(initialState, moveBlock([0, tests[i].from], [0, tests[i].to]));

      expect(result.editors[0].order).toEqual(expectedOrder);
    }
  });
});
