import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
  static targets = [
    'input', 'productName', 'productDescription',
    'description', 'unitPrice'
  ]
  
  static values = {
    products: String
  }
  
  connect() {
    this.currentFocus = -1
    this.checkInputs()
    this.products = JSON.parse(JSON.parse(this.productsValue));
  }
  
  search() {
    this.closeAllLists()
    let val = this.inputTarget.value
    if (!val) return false
    
    this.currentFocus = -1
    const dropdown = document.createElement("DIV")
    dropdown.setAttribute("id", this.inputTarget.id + "autocomplete-list")
    dropdown.setAttribute("class", "autocomplete-items")
    this.element.appendChild(dropdown)
    
    let matchFound = false
    for (let product of this.products) {
      if (product.name.toLowerCase().indexOf(val.toLowerCase()) > -1) {
        matchFound = true
        let item = document.createElement("DIV")
        let start = product.name.toLowerCase().indexOf(val.toLowerCase())
        let end = start + val.length
        item.innerHTML = product.name.substr(0, start) + 
            "<strong>" + product.name.substr(start, val.length) + "</strong>" +
            product.name.substr(end)
        
        item.dataset.name = product.name
        item.dataset.price = product.unitPrice
        item.dataset.description = product.description
        
        item.addEventListener("click", () => {
          this.selectItem(item)
        })
        
        dropdown.appendChild(item)
      }
    }
    
    if (!matchFound) {
      this.productNameTarget.value = this.inputTarget.value
      this.descriptionTarget.value = "Produit personnalisé"
      this.productDescriptionTarget.value = "Description par défaut"
    }
  }
  
  selectItem(item) {
    this.inputTarget.value = item.textContent
    this.productNameTarget.value = item.dataset.name
    this.descriptionTarget.value = item.dataset.description || "Description non disponible"
    this.productDescriptionTarget.value = item.dataset.description || "Description par défaut"
    
    // Find the unitPrice input either as a target or outside the controller
    if (item.dataset.price) {
      const price = parseFloat(item.dataset.price)
      if (!isNaN(price)) {
        // Check if we have a unitPrice target
        if (this.hasUnitPriceTarget) {
          this.unitPriceTarget.value = price.toFixed(2)
          
          // Déclencher l'événement input natif pour que les listeners standard soient activés
          const inputEvent = new Event('input', { bubbles: true })
          this.unitPriceTarget.dispatchEvent(inputEvent)
          
          // Déclencher manuellement le calcul sur le contrôleur line_item si présent
          const lineItemController = this.application.getControllerForElementAndIdentifier(
            this.element.closest('.line-item'),
            'line-item'
          )
          
          if (lineItemController && typeof lineItemController.calculate === 'function') {
            lineItemController.calculate()
          }
        } else {
          // Try to find a related unit price input if not a direct target
          const lineItem = this.element.closest('.line-item')
          if (lineItem) {
            const unitPriceInput = lineItem.querySelector('.unit-price-input')
            if (unitPriceInput) {
              unitPriceInput.value = price.toFixed(2)
              
              // Trigger input event
              const inputEvent = new Event('input', { bubbles: true })
              unitPriceInput.dispatchEvent(inputEvent)
            }
          }
        }
      }
    }
    
    this.closeAllLists()
  }
  
  navigateList(event) {
    let x = document.getElementById(this.inputTarget.id + "autocomplete-list")
    if (!x) return
    
    x = x.getElementsByTagName("div")
    
    if (event.keyCode == 40) { // down
      this.currentFocus++
      this.addActive(x)
    } else if (event.keyCode == 38) { // up
      this.currentFocus--
      this.addActive(x)
    } else if (event.keyCode == 13) { // enter
      event.preventDefault()
      if (this.currentFocus > -1) {
        if (x) x[this.currentFocus].click()
      }
    }
  }
  
  blur() {
    setTimeout(() => {
      this.closeAllLists()
    }, 200)
    
    this.checkInputs()
  }
  
  checkInputs() {
    if (!this.productNameTarget.value) {
      this.productNameTarget.value = this.inputTarget.value
    }
    
    if (!this.descriptionTarget.value) {
      this.descriptionTarget.value = "Produit personnalisé"
    }
    
    if (!this.productDescriptionTarget.value) {
      this.productDescriptionTarget.value = "Description par défaut"
    }
  }
  
  addActive(x) {
    if (!x) return false
    this.removeActive(x)
    if (this.currentFocus >= x.length) this.currentFocus = 0
    if (this.currentFocus < 0) this.currentFocus = (x.length - 1)
    x[this.currentFocus].classList.add("autocomplete-active")
  }
  
  removeActive(x) {
    for (let i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active")
    }
  }
  
  closeAllLists(elmnt) {
    const items = document.getElementsByClassName("autocomplete-items")
    for (let i = 0; i < items.length; i++) {
      if (elmnt != items[i] && elmnt != this.inputTarget) {
        items[i].parentNode.removeChild(items[i])
      }
    }
  }
}