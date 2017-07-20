import {newStore, addEditor} from './store';

describe('editor store', () => {
  it('is a redux store', () => {
    expect(typeof newStore().dispatch).toEqual('function');
  });

  it('has initial state', () => {
    expect(newStore().getState()).toEqual({
      blocks: {},
      editors: [],
    });
  });

  it('can register new editors', () => {
    const store = newStore();
    expect(addEditor(store)).toEqual(0);
    expect(store.getState()).toEqual({
      blocks: {},
      editors: [
        {
          contentId: undefined,
          loaded: false,
          order: []
        }
      ],
    });

    expect(addEditor(store, 'some-content-guid')).toEqual(1);
    expect(store.getState()).toEqual({
      blocks: {},
      editors: [
        {
          contentId: undefined,
          loaded: false,
          order: []
        },
        {
          contentId: 'some-content-guid',
          loaded: false,
          order: []
        }
      ],
    });
  });
});
