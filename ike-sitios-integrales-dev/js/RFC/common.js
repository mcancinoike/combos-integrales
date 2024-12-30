export function removeAccents(input) {
  return input.normalize("NFD").replace(/[\u0300-\u036f]/g, "")
}