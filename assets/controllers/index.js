import { Application } from '@hotwired/stimulus'

// Import controllers explicitly
import QuoteFormController from './quote_form_controller.js'
import LineItemController from './line_item_controller.js'
import AutocompleteController from './autocomplete_controller.js'

// Initialize Stimulus application
const application = Application.start()

// Register controllers manually
application.register('quote-form', QuoteFormController)
application.register('line-item', LineItemController)
application.register('autocomplete', AutocompleteController)