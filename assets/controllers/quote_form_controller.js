import { Controller } from '@hotwired/stimulus'
import flatpickr from 'flatpickr'

export default class extends Controller {
  static targets = [
    'lineItemsBody', 'subtotal', 'total', 
    'form', 'lineItemTemplate'
  ]
  
  static values = {
    products: Array
  }

  initialize() {
    this.isProcessing = false
    this.lineItemCounter = 0
  }

  connect() {
    this.initDatepickr()
    this.updateTotals()
    
    // Compter le nombre initial de lignes
    this.lineItemCounter = this.element.querySelectorAll('.line-item').length
  }
  
  initDatepickr() {
    flatpickr(this.element.querySelectorAll('.datepicker'), {
      dateFormat: 'Y-m-d',
      allowInput: true
    })
  }
  
  addLineItem(event) {
    // Bloquer complètement l'événement et utiliser une méthode native
    if (event) {
      event.preventDefault()
      event.stopImmediatePropagation()
    }
    
    // Protection contre les clics multiples
    if (this.isProcessing) return
    this.isProcessing = true
    
    // Utiliser un ID unique basé sur un compteur interne plutôt que de compter les éléments
    const newIndex = this.lineItemCounter++
    
    try {
      // Créer l'élément à partir du template mais sans l'insérer tout de suite
      const template = this.lineItemTemplateTarget.innerHTML
        .replace(/__index__/g, newIndex)
      
      // Insérer l'élément dans le DOM
      this.lineItemsBodyTarget.insertAdjacentHTML('beforeend', template)
      
      // Initialiser les controllers Stimulus sur le nouvel élément
      setTimeout(() => {
        this.application.getControllerForElementAndIdentifier(
          this.lineItemsBodyTarget.lastElementChild, 
          'line-item'
        )?.initialize()
      }, 50)
      
      // Mettre à jour les totaux
      this.updateTotals()
    } catch (error) {
      console.error("Erreur lors de l'ajout d'une ligne:", error)
    } finally {
      // Réinitialiser le verrou après un délai suffisant
      setTimeout(() => {
        this.isProcessing = false
      }, 500)
    }
  }
  
  updateTotals() {
    let subtotal = 0
    
    this.element.querySelectorAll('.line-item').forEach(row => {
      const amountDisplay = row.querySelector('.amount-input')
      let amount = 0
      
      if (amountDisplay.tagName === 'INPUT') {
        amount = parseFloat(amountDisplay.value) || 0
      } else {
        const amountText = amountDisplay.textContent.replace(' €', '').replace(',', '.')
        amount = parseFloat(amountText) || 0
      }
      
      subtotal += amount
    })
    
    this.subtotalTarget.textContent = subtotal.toFixed(2).replace('.', ',') + ' €'
    this.totalTarget.textContent = subtotal.toFixed(2).replace('.', ',') + ' €'
  }
  
  submitForm(event) {
    const lineItems = this.element.querySelectorAll('.line-item')
    
    // Vérifier tous les champs productDescription
    lineItems.forEach(row => {
      const container = row.querySelector('.autocomplete-container')
      const productNameInput = container.querySelector('.product-name-input')
      const productDescInput = container.querySelector('.product-description-input')
      
      if (!productDescInput || !productDescInput.value) {
        if (!productDescInput) {
          const newInput = document.createElement('input')
          newInput.type = 'hidden'
          newInput.name = productNameInput.name.replace('productName', 'productDescription')
          newInput.className = 'product-description-input'
          newInput.value = 'Description par défaut'
          container.appendChild(newInput)
        } else {
          productDescInput.value = 'Description par défaut'
        }
      }
    })
    
    if (lineItems.length === 0) {
      alert('Vous devez ajouter au moins une ligne de devis.')
      event.preventDefault()
      return false
    }
    
    return true
  }
  
  updateLineItemIndexes() {
    this.element.querySelectorAll('.line-item').forEach((row, index) => {
      row.querySelectorAll('input[name*="quoteLines"]').forEach(input => {
        const name = input.getAttribute('name')
        if (name) {
          const newName = name.replace(/quote\[quoteLines\]\[\d+\]/, `quote[quoteLines][${index}]`)
          input.setAttribute('name', newName)
        }
      })
    })
  }
}