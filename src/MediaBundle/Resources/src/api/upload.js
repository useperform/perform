import axios from 'axios'

const defaultOptions = {
  url: '/admin/media/upload',
  start: 0,
  chunkSize: 1024 * 1024, // 1MB
  complete() {},
  progress() {},
  error() {},
};

const upload = function(file, options) {
  let opts = Object.assign({}, defaultOptions, options);
  if (!opts.end) {
    // byte to slice up to, this byte will NOT be included
    opts.end = file.size < opts.chunkSize ? file.size : opts.chunkSize;
  }
  let formData = new FormData();
  formData.append("file", file.slice(opts.start, opts.end), file.name);
  return axios.post(opts.url, formData, {
    headers: {
      'Content-Range': 'bytes '+opts.start+'-'+(opts.end-1)+'/'+file.size,
    }
  }).then(function (response) {
    opts.progress(parseInt(opts.end / file.size * 100, 10), response);
    if (opts.end === file.size) {
      opts.complete(response);
    } else {
      return upload(file, Object.assign({}, opts, {
        start: opts.start + opts.chunkSize,
        end: (opts.end + opts.chunkSize) > file.size ? file.size : (opts.end + opts.chunkSize),
      }));
    }
  }).catch(function (error) {
    opts.error(error.response);
  });
};

export default upload;
