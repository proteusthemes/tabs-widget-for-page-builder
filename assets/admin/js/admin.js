/**
 * Admin dashboard JS code
 */
(function( $ ) {

	// Make tabs settings foldable.
	$(document).on( 'click', '.pt-tab-setting__toggle', function() {
		$( this ).toggleClass( 'dashicons-minus dashicons-plus' );
		$( this ).closest( '.pt-tab-setting' ).find( '.pt-tab-setting__content' ).toggle();
	});

	// Update tab setting header on tab title change.
	$(document).on( 'change', '.js-pt-tab-setting-title', function() {
		$( this ).closest( '.pt-tab-setting' ).find( '.pt-tab-setting__header-title' ).text( $( this ).val() );
	});

})( jQuery );

/********************************************************
 			Backbone code for repeating fields in widget
********************************************************/

// Namespace for Backbone elements
window.PTTabs = {
	Models:    {},
	ListViews: {},
	Views:     {},
	Utils:     {},
};

/**
 ******************** Backbone Models *******************
 */

_.extend( PTTabs.Models, {
	Tab: Backbone.Model.extend( {
		defaults: {
			'title':       '',
			'builder_id':  '',
			'panels_data': '',
		}
	} ),
} );

/**
 ******************** Backbone Views *******************
 */

// Generic single view that others can extend from
PTTabs.Views.Abstract = Backbone.View.extend( {
	initialize: function ( params ) {
		this.templateHTML = params.templateHTML;

		return this;
	},

	render: function () {
		this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );

		return this;
	},

	destroy: function ( ev ) {
		ev.preventDefault();

		this.remove();
		this.model.trigger( 'destroy' );
	},
} );

_.extend( PTTabs.Views, {

	// View of a single tab
	Tab: PTTabs.Views.Abstract.extend( {
		className: 'pt-widget-single-tab',

		events: {
			'click .js-pt-remove-tab': 'destroy',
		},

		render: function () {
			this.model.set( 'panels_data', JSON.stringify( this.model.get('panels_data') ) );
			this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );

			return this;
		},
	} ),

} );


/**
 ******************** Backbone ListViews *******************
 *
 * Parent container for multiple view nodes.
 */

PTTabs.ListViews.Abstract = Backbone.View.extend( {

	initialize: function ( params ) {
		this.widgetId     = params.widgetId;
		this.itemsModel   = params.itemsModel;
		this.itemView     = params.itemView;
		this.itemTemplate = params.itemTemplate;

		// Cached reference to the element in the DOM
		this.$items = this.$( params.itemsClass );

		// Collection of items
		this.items = new Backbone.Collection( [], {
			model: this.itemsModel
		} );

		// Listen to adding of the new items
		this.listenTo( this.items, 'add', this.appendOne );

		return this;
	},

	addNew: function ( ev ) {
		ev.preventDefault();

		var currentMaxId = this.getMaxId();

		this.items.add( new this.itemsModel( {
			id: (currentMaxId + 1)
		} ) );

		return this;
	},

	getMaxId: function () {
		if ( this.items.isEmpty() ) {
			return -1;
		}
		else {
			var itemWithMaxId = this.items.max( function ( item ) {
				return parseInt( item.id, 10 );
			} );

			return parseInt( itemWithMaxId.id, 10 );
		}
	},

	appendOne: function ( item ) {
		var renderedItem = new this.itemView( {
			model:        item,
			templateHTML: jQuery( this.itemTemplate + this.widgetId ).html()
		} ).render();

		var currentWidgetId = this.widgetId;

		// If the widget is in the initialize state (hidden), then do not append a new item
		if ( '__i__' !== currentWidgetId.slice( -5 ) ) {
			this.$items.append( renderedItem.el );
		}

		return this;
	}
} );


_.extend( PTTabs.ListViews, {

	// Collection of all tabs, but associated with each individual widget
	Tabs: PTTabs.ListViews.Abstract.extend( {
		events: {
			'click .js-pt-add-tab': 'addNew'
		},

		// Overwrite the appendOne function to setup the layout builder
		appendOne: function ( item ) {
			// Set an unique ID for a new tab (will be used in the div id)
			item.attributes.builder_id = _.uniqueId('layout-builder-');

			var renderedItem = new this.itemView( {
				model:        item,
				templateHTML: jQuery( this.itemTemplate + this.widgetId ).html()
			} ).render();

			var currentWidgetId = this.widgetId;

			// If the widget is in the initialize state (hidden), then do not append a new item
			if ( '__i__' !== currentWidgetId.slice( -5 ) ) {
				this.$items.append( renderedItem.el );
			}

			// Setup the Page Builder layout builder
			if(typeof jQuery.fn.soPanelsSetupBuilderWidget != 'undefined' && !jQuery('body').hasClass('wp-customizer')) {
				jQuery( "#siteorigin-page-builder-widget-" + item.attributes.builder_id ).soPanelsSetupBuilderWidget();
			}

			return this;
		}
	} ),
} );


/**
 ******************** Repopulate Functions *******************
 */

_.extend( PTTabs.Utils, {
	// Generic repopulation function used in all repopulate functions
	repopulateGeneric: function ( collectionType, parameters, json, widgetId ) {
		var collection = new collectionType( parameters );

		// Convert to array if needed
		if ( _( json ).isObject() ) {
			json = _( json ).values();
		}

		// Add all items to collection of newly created view
		collection.items.add( json, { parse: true } );
	},

	/**
	 * Function which adds the existing tabs to the DOM
	 * @param  {json} tabsJSON
	 * @param  {string} widgetId ID of widget from PHP $this->id
	 * @return {void}
	 */
	repopulateTabs: function ( tabsJSON, widgetId ) {
		var parameters = {
			el:           '#tabs-' + widgetId,
			widgetId:     widgetId,
			itemsClass:   '.tabs',
			itemTemplate: '#js-pt-tab-',
			itemsModel:   PTTabs.Models.Tab,
			itemView:     PTTabs.Views.Tab,
		};

		this.repopulateGeneric( PTTabs.ListViews.Tabs, parameters, tabsJSON, widgetId );
	},
} );