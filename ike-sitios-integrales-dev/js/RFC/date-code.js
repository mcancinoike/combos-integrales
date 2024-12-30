export function dateCode(day, month, year) {
  return year.toString().slice(-2) + zeroPadded(month) + zeroPadded(day)
}

function zeroPadded(n) {
  return ("00" + n).slice(-2)
}
