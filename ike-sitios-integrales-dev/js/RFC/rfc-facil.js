import homoclave from "./homoclave"
import verificationDigit from "./verification-digit"
import { naturalPersonTenDigitsCode } from "./natural-person-tdc-code"
//import { juristicPersonTenDigitsCode } from "./juristic-person-tdc-code"

export default class RfcFacil {
  static forNaturalPerson(person) {
    const t = naturalPersonTenDigitsCode(person)
    const h = homoclave(naturalPersonFullName(person))
    const v = verificationDigit(t + h)
    return t + h + v
  }

/*  static forJuristicPerson(person) {
    const t = juristicPersonTenDigitsCode(person)
    const h = homoclave(person.name)
    const v = verificationDigit(" " + t + h)
    return t + h + v
  }*/
}

function naturalPersonFullName(p) {
  return `${p.firstLastName} ${p.secondLastName} ${p.name}`
}
