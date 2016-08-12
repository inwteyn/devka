
/* $Id: Quiz.js 2010-05-25 01:44 michael $ */


Wall.Composer.Plugin.AVP = new Class({

  Extends : Wall.Composer.Plugin.Interface,

  name : 'avp',

  options :
  {
      title : en4.core.language.translate('Add Video')
  },

  initialize : function(options) {
    this.elements = new Hash(this.elements);
    this.params = new Hash(this.params);
    this.parent(options);
  },

  attach : function() {
    this.parent();
    this.makeActivator();
    //this.elements.activator.addClass('smoothbox');
    //this.elements.activator.set('href', en4.core.basePath+'vids/choose');
    
    return this;
  },

  detach : function() {
    this.parent();
    return this;
  },

  activate : function() {
    if( this.active ) return;
    this.parent();

    this.makeMenu();
    this.makeBody();
    
    var self = this;

    if (this.options.import_allowed)
    {
          this.elements.chooseImportButton = new Element('a', {
            'id' : 'avp-feed-import',
            'style' : 'display: inline; margin-left: 10px;',
            'html' : en4.core.language.translate('TOUCH_Import'),
            'href' : en4.core.basePath+'vids/feed-import/?format=smoothbox',
            'class' : 'smoothbox'
          }).inject(this.elements.body);
          
          this.elements.chooseImportButton.addEvent('click', function()
          {
            self.deactivate();
          });
          
          Touch.bind($$('.wall-compose-avp-body')[0]);
    }
  },

  deactivate : function() {
    if( !this.active ) return;
    this.parent();

    this.request = false;
  }
});