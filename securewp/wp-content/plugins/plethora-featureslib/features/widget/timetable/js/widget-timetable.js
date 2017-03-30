// DRAG AND DROP SORTABLE WORKTABLE LIST //

jQuery(function($) {

  window.WidgetTimetable = {
    rowDataID: {}
  };

  WidgetTimetable.updateRowData = function(rowData){

    var twID = rowData.twID;
    rowData  = encodeURIComponent(JSON.stringify(rowData));
    WidgetTimetable.rowDataID[twID].val(rowData);

  };

  WidgetTimetable.EventBus = _({}).extend(Backbone.Events);

  WidgetTimetable.Item = Backbone.Model.extend({
    defaults: { day: "Day", time: "00:00" }
  });

  WidgetTimetable.ItemView = Backbone.View.extend({
    tagName: 'li',
    className: 'item-view',
    template: (function(str) {
      var orig_settings, t;
      orig_settings = _.templateSettings;
      _.templateSettings = {
        interpolate: /\{\{(.+?)\}\}/g
      };
      t = _.template($("#widgetTimetableTemplate").html());
      _.templateSettings = orig_settings;
      return t;
    })(),
    events: {
      'drop'         : 'drop',
      'keyup input'  : 'updateModel',
      'click button' : 'removeModel'
    },
    removeModel: function(e) {
      return WidgetTimetable.EventBus.trigger('removeModel', this.model);
    },
    updateModel: function(e) {
      this.model.set(e.target.name, e.target.value);
      return WidgetTimetable.EventBus.trigger('updateModel');
    },
    drop: function(event, index) {
      this.$el.trigger('update-sort', [this.model, index]);
    },
    render: function() {
      $(this.el).html(this.template(this.model.attributes));
      this.$el.css({ 'cursor' : 'move', 'border' : '1px solid #ccc', 'padding' : '10px', 'margin' : '1px auto 1px auto' });
      return this;
    }
  });


  WidgetTimetable.Items = Backbone.Collection.extend({
    model: WidgetTimetable.Item,
    comparator: function(model) {
      return model.get('ordinal');
    },
    initialize: function( models, options ) {
      this.listenTo(WidgetTimetable.EventBus, 'removeModel', this.onModelRemoved);
      this.listenTo(WidgetTimetable.EventBus, 'updateModel', this.onModelUpdated);
      options || ( options = {} )
      if ( options.twID ) this.twID = options.twID;
    },
    onModelRemoved: function(model, collection, options) {
      this.remove(model);
    },
    onModelUpdated: function() {
      WidgetTimetable.updateRowData(this);
    }
  });

  WidgetTimetable.ItemsView = Backbone.View.extend({
    events: {
      'update-sort': 'updateSort'
    },
    initialize: function() {
      this.collection.bind('add', this.addRow, this);
      return this.listenTo(WidgetTimetable.EventBus, 'removeModel', this.removeModel);
    },
    removeModel: function() {
      WidgetTimetable.updateRowData(this.collection);
      return this.render();
    },
    addRow: function(model) {
      return this.render();
    },
    render: function() {
      this.$el.children().remove();
      this.collection.each(this.appendModelView, this);
      return this;
    },
    appendModelView: function(model) {
      var el;
      el = new WidgetTimetable.ItemView({
        model: model
      }).render().el;
      this.$el.append(el);
    },
    updateSort: function(event, model, position) {
      this.collection.remove(model);
      this.collection.each(function(model, index) {
        var ordinal;
        ordinal = index;
        if (index >= position) {
          ordinal += 1;
        }
        model.set('ordinal', ordinal);
      });
      model.set('ordinal', position);
      this.collection.add(model, {
        at: position
      });
      this.render();
    }
  });

  WidgetTimetable.Init = function( options ){

    // GET ALL TIMETABLE SORTABLE SECTIONS
    var timetableWidgets = $("#widgets-right")
      .find("input[name^='widget-plethora-timetable']")
      .filter("input[name$='[rowData]']");

    if ( timetableWidgets.length < 1 ) return;

    timetableWidgets.each(function(index, tw){

      var $tw = $(tw);
      var $_id = $tw.attr("id").match(/\d+/);

      if ( typeof $_id === "null" ) return;

      $_id = $_id[0];

      // WidgetTimetable.rowDataID = $tw; // DEPRECATED TO FIX MULTIPLE WIDGETS BUG
      WidgetTimetable.rowDataID[$_id] = $tw;

      var $widgetInput    = $("[id$=plethora-timetable-widget-" + $_id + "]");
      var $widgetControls = $widgetInput.find('.widgetTimetableControls');

      var Instance                = {};
          Instance.twID           = $_id;
          Instance.collection     = new WidgetTimetable.Items([], { twID: $_id });
          Instance.collectionView = new WidgetTimetable.ItemsView({

            el: $widgetControls,
            collection: Instance.collection

          });

      var widgetRowData = $tw.val();

      if ( widgetRowData ){
        widgetRowData = JSON.parse( decodeURIComponent( widgetRowData ) ); 
        Instance.collection.add( widgetRowData );
      } 

      Instance.collectionView.render();

      $widgetControls.sortable({
        stop: function(event, ui) {

          ui.item.trigger('drop', ui.item.index());
          WidgetTimetable.updateRowData(Instance.collection);

        }
      });

      $widgetInput.find('.widgetTimetableAddRow').on('click', function(){

        Instance.collection.add( new WidgetTimetable.Item({  ordinal: Instance.collection.length  }) );
        WidgetTimetable.updateRowData(Instance.collection);

      });

    });

  };

  WidgetTimetable.Init();

  $(document).on('widget-updated widget-added', function(e){  WidgetTimetable.Init();  });

});