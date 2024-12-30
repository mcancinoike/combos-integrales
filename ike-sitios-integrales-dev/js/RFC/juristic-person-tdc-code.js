import { dateCode } from "./date-code"
import { removeAccents } from "./common"
// @ts-ignore
import writtenNumber from "written-number"
// @ts-ignore
import romanNumerals from "roman-numerals"

// higher order function
const pipe = (...ops) => ops.reduce((a, b) => arg => b(a(arg)))

// higher order function
const flatMap = fn => words =>
    words.reduce((acc, w) => {
      acc.push(...fn(w))
      return acc
    }, [])

const toUpperCase = s => s.toUpperCase()

const trim = s => s.trim()

const normalize = pipe(toUpperCase, removeAccents, trim)

const ignoreJuristicPersonTypeAbbreviations = input =>
    input
        .replace(/S\.?\s?EN\s?N\.?\s?C\.?$/g, "")
        .replace(/S\.?\s?EN\s?C\.?\s?POR\s?A\.?$/g, "")
        .replace(/S\.?\s?EN\s?C\.?$/g, "")
        .replace(/S\.?\s?DE\s?R\.?\s?L\.?$/g, "")
        .replace(/S\.?\s?DE\s?R\.?\s?L\.?\s?DE\s?C\.?\s?V\.?$/g, "")
        .replace(/S\.?\s?A\.?\s?DE\s?C\.?\s?V\.?$/g, "")
        .replace(/S\.?\s?A\.?\s?P\.?\s?I\.?\s?DE\s?C\.?\s?V\.?$/g, "")
        .replace(/S\.?\s?A\.?\s?S\.?\s?DE\s?C\.?\s?V\.?$/g, "")
        .replace(/A\.?\s?EN\s?P\.?$/g, "")
        .replace(/S\.?\s?C\.?\s?[LPS]\.?$/g, "")
        .replace(/S\.?\s?[AC]\.?$/g, "")
        .replace(/S\.?\s?N\.?\s?C\.?$/g, "")
        .replace(/A\.?\s?C\.?$/g, "")

const removeEmptyWords = w => w.length > 0

const splitWords = input => input.split(/[,\s]+/).filter(removeEmptyWords)

/*
 * This list is based on Anexo V from the official documentation
 * but some words have been commented out because the examples from
 * the same documentation contradict the list
 */
const forbiddenWords = [
  "EL",
  "LA",
  "DE",
  "LOS",
  "LAS",
  "Y",
  "DEL",
  "MI",
  "POR",
  "CON",
  /*'AL',*/ "SUS",
  "E",
  "PARA",
  "EN",
  "MC",
  "VON",
  "MAC",
  "VAN",
  "COMPANIA",
  "CIA",
  "CIA.",
  "SOCIEDAD",
  "SOC",
  "SOC.",
  "COMPANY",
  "CO",
  /*'COOPERATIVA', 'COOP',*/
  "SC",
  "SCL",
  "SCS",
  "SNC",
  "SRL",
  "CV",
  "SA",
  "THE",
  "OF",
  "AND",
  "A"
]

const ignoreForbiddenWords = words =>
    words.filter(w => forbiddenWords.indexOf(w) === -1)

const markOneLetterAbbreviations = words =>
    words.map(w => w.replace(/^([^.])\./g, "$1AABBRREEVVIIAATTIIOONN"))

const expandSpecialCharactersInSingletonWord = flatMap(w => {
  if (w.length === 1) {
    return w
        .replace("@", "ARROBA")
        .replace("´", "APOSTROFE")
        .replace("%", "PORCIENTO")
        .replace("#", "NUMERO")
        .replace("!", "ADMIRACION")
        .replace(".", "PUNTO")
        .replace("$", "PESOS")
        .replace('"', "COMILLAS")
        .replace("-", "GUION")
        .replace("/", "DIAGONAL")
        .replace("+", "SUMA")
        .replace("(", "ABRE PARENTESIS")
        .replace(")", "CIERRA PARENTESIS")
        .split(" ")
        .filter(removeEmptyWords)
  }
  return [w]
})

const ignoreSpecialCharactersInWords = words =>
    words.map(w => w.replace(/(.+?)[@´%#!.$"-/+()](.+?)/g, "$1$2"))

const splitOneLetterAbbreviations = flatMap(w =>
    w.split("AABBRREEVVIIAATTIIOONN").filter(removeEmptyWords)
)

const expandSingleArabicNumeral = numeral =>
    writtenNumber(parseInt(numeral, 10), { lang: "es" })
        .toUpperCase()
        .split(/\s/)
        .filter(removeEmptyWords)

const expandArabicNumerals = flatMap(word => {
  if (word.match(/[0-9]+/)) {
    return expandSingleArabicNumeral(word)
  }
  return [word]
})

const expandRomanNumerals = flatMap(word => {
  if (
      word.match(/^(M{0,4})(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$/)
  ) {
    return expandSingleArabicNumeral(romanNumerals.toArabic(word))
  }
  return [word]
})

const threeDigitsCode = words => {
  if (words.length >= 3) {
    return "" + words[0].charAt(0) + words[1].charAt(0) + words[2].charAt(0)
  } else if (words.length === 2) {
    return "" + words[0].charAt(0) + words[1].substring(0, 2)
  } else {
    return firstThreeCharactersWithRightPad(words[0])
  }
}

const firstThreeCharactersWithRightPad = word =>
    word.length >= 3 ? word.substring(0, 3) : word.padEnd(3, "X")

const nameCode = pipe(
    normalize,
    ignoreJuristicPersonTypeAbbreviations,
    splitWords,
    ignoreForbiddenWords,
    markOneLetterAbbreviations,
    expandSpecialCharactersInSingletonWord,
    ignoreSpecialCharactersInWords,
    splitOneLetterAbbreviations,
    expandArabicNumerals,
    expandRomanNumerals,
    threeDigitsCode
)

export const juristicPersonTenDigitsCode = person =>
    nameCode(person.name) + dateCode(person.day, person.month, person.year)
