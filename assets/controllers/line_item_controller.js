import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
  static targets = [
    'quantity', 'unitPrice', 'discount', 
    'amount', 'productName', 'productDescription', 
    'description'
  ]
  
  connect() {
    this.calculateAmount()
  }
  
  calculate() {
    this.calculateAmount()
    // Dispatch un événement pour mettre à jour les totaux
    this.dispatch('calculationComplete')
  }
  
  calculateAmount() {
    const quantity = parseFloat(this.quantityTarget.value) || 0
    const unitPrice = parseFloat(this.unitPriceTarget.value) || 0
    const discount = parseFloat(this.discountTarget.value) || 0
    
    const amount = quantity * unitPrice * (1 - discount / 100)
    
    if (this.amountTarget.tagName === 'INPUT') {
      this.amountTarget.value = amount.toFixed(2)
    } else {
      this.amountTarget.textContent = amount.toFixed(2).replace('.', ',') + ' €'
    }
  }
  
  delete(event) {
    // Empêcher le comportement par défaut et la propagation
    event.preventDefault()
    event.stopPropagation()
    
    const lineItems = document.querySelectorAll('.line-item')
    if (lineItems.length > 1) {
      this.element.remove()
      // Dispatch un événement pour mettre à jour les index et totaux
      this.dispatch('lineItemRemoved')
    } else {
      alert('Vous devez avoir au moins une ligne de devis.')
    }
  }
}