import store from './store';

describe('editor store', () => {
  it('is a redux store', () => {
    expect(typeof store.dispatch).toEqual('function');
  });
});
