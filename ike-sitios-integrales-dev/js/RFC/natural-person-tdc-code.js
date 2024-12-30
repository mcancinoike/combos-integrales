import { dateCode } from "./date-code"
import { removeAccents } from "./common"

export function naturalPersonTenDigitsCode(person) {
  return new NameCode(person).toString() + birthdayCode(person)
}

// matches any ocurrence of the special particles as a word: '^foo | foo | foo$''
const specialParticlesRegex = new RegExp(
    "(?:" +
    ["DE", "LA", "LAS", "MC", "VON", "DEL", "LOS", "Y", "MAC", "VAN", "MI"]
        .map(p => `^${p} | ${p} | ${p}$`)
        .join("|") +
    ")",
    "g"
)

function birthdayCode(person) {
  return dateCode(person.day, person.month, person.year)
}

class NameCode {
  constructor(person) {
    this.person = person
    this.filteredPersonName = this.getFilteredPersonName()
  }

  toString() {
    return this.obfuscateForbiddenWords(this.calculateCode())
  }

  calculateCode() {
    if (this.isEmpty(this.person.firstLastName)) {
      return (
          this.normalize(this.person.secondLastName).substring(0, 2) +
          this.filteredPersonName.substring(0, 2)
      )
    } else if (this.isEmpty(this.person.secondLastName)) {
      return (
          this.normalize(this.person.firstLastName).substring(0, 2) +
          this.filteredPersonName.substring(0, 2)
      )
    } else if (this.isFirstLastNameIsTooShort()) {
      return (
          this.normalize(this.person.firstLastName).charAt(0) +
          this.normalize(this.person.secondLastName).charAt(0) +
          this.filteredPersonName.substring(0, 2)
      )
    } else {
      return (
          this.normalize(this.person.firstLastName).charAt(0) +
          this.firstVowelExcludingFirstCharacterOf(
              this.normalize(this.person.firstLastName)
          ) +
          this.normalize(this.person.secondLastName).charAt(0) +
          this.filteredPersonName.charAt(0)
      )
    }
  }

  obfuscateForbiddenWords(s) {
    const match =
        s.match(
            /(BUE[IY]|CAC[AO]|CAGA|KOGE|KAKA|MAME|KOJO|[KQ]ULO|CAGO|CO[GJ]E|COJO|FETO|JOTO|KA[CG]O)/
        ) ||
        s.match(/(MAMO|MEAR|M[EI]ON|MOCO|MULA|PED[AO]|PENE|PUT[AO]|RATA|RUIN)/)
    return match ? s.substring(0, 3) + "X" : s
  }

  // filter out common names (if more than one is provided)
  getFilteredPersonName() {
    const normalized = this.normalize(this.person.name)
    if (this.person.name.split(" ").length > 1) {
      return normalized.replace(/^(JOSE|MARIA|MA|MA\.)\s+/i, "")
    }
    return normalized
  }

  normalize(s) {
    return removeAccents(s.toUpperCase())
        .replace(/\s+/g, "  ") // double space to allow multiple special-particles matching
        .replace(specialParticlesRegex, "")
        .replace(/\s+/g, " ") // reset space
        .trim()
  }

  firstVowelExcludingFirstCharacterOf(s) {
    let result = /[aeiou]/i.exec(s.slice(1))
    if (!result) {
      throw new Error("")
    }
    return result[0]
  }

  isFirstLastNameIsTooShort() {
    return this.normalize(this.person.firstLastName).length <= 2
  }

  isEmpty(s) {
    return (
        s === null || typeof s === "undefined" || this.normalize(s).length === 0
    )
  }
}
