const replacements = [
  [/dd/, 'DD'],
  [/yyyy/, 'YYYY'],
  [/y/, 'YYYY'],
];
// transform an ICU date to the format used by date-fns.
// http://userguide.icu-project.org/formatparse/datetime
// https://date-fns.org/v2.0.0-alpha.7/docs/parse
// this may not be needed when version 2 stable is released
// https://github.com/date-fns/date-fns/issues/520
export function ICUtoDateFns(format) {
  for (let r of replacements) {
    format = format.replace(r[0], r[1]);
  }

  return format;
}

export function parseTimeString(string) {
  if (string.trim().match(/[^\d:]/) || string.length < 1) {
    throw 'ParseException';
  }
  let test = string.replace(':', '').trim();
  //1 or 2 digits h or hh
  if (test.length < 3) {
    return {
      hours: parseInt(test),
      minutes: 0,
      seconds: 0
    };
  }
  //3 digits, h:mm
  if (test.length === 3) {
    return {
      hours: parseInt(test.slice(0, 1)),
      minutes: parseInt(test.slice(1)),
      seconds: 0
    };
  }
  //4 digits, hh:mm
  if (test.length === 4) {
    return {
      hours: parseInt(test.slice(0, 2)),
      minutes: parseInt(test.slice(2)),
      seconds: 0
    };
  }

  return {
    hours: parseInt(test.slice(0, 2)),
    minutes: parseInt(test.slice(2, 4)),
    seconds: parseInt(test.slice(4, 6))
  };
}
