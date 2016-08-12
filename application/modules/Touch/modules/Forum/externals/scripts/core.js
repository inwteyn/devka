/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 12.10.11
 * Time: 15:24
 * To change this template use File | Settings | File Templates.
 */
var PostReply = new Class({
  posts: null,
  scroll:null,
  posts_items: null,
  quoteReplying: {
    quote_grab: null,
    quote_div: null,
    blockquote: null,
    strong: null,
    quote_arrow: null,
    close_quoting: null
  },

  initialize: function(){
    // Alias of the object
    var self = this;

    this.initQuoteElements()
    // Bind Events

    this.scroll = new Fx.Scroll(document.body, {
            wait: false,
            duration: 1000,
            offset: {'x': -200, 'y': -50},
            transition: Fx.Transitions.Quad.easeInOut
          });

  },
  reply: function(){

  },
  initQuoteElements: function(){
    this.quoteReplying.quote_div = new Element('div',
      {
        'class': 'forum_post_reply_quote forum_topic_posts_info_body'
      }
    );
    this.quoteReplying.quote_grab = new Element('div', {'class': 'q_div_grab'});
    this.quoteReplying.blockquote = new Element('blockquote');
    this.quoteReplying.strong = new Element('strong');
    this.quoteReplying.quote_arrow = new Element('div', {'class': 'forum_post_quick_quote_arrow'});
    this.quoteReplying.close_quoting = new Element('div', {'class': 'close_quoting'});
  },
  quoteReply: function(post_id){
    var self = this;
    var form = $('forum_post_quick');
    form.setStyle('display', 'block');
    var ta = form.getElement('#body_c');
    var old_quote = form.getElement('.q_div_grab');
    var old_quote_arrow = form.getElement('.forum_post_quick_quote_arrow');
    if(old_quote){
      var old_close_btn = old_quote.getElement('.close_quoting');
      old_close_btn.removeEvents('click');
      old_quote.dispose();
      old_quote_arrow.dispose();
      ta.dispose();
    }

    var quote_post = $('forum_post_'+post_id);

    // Get Author's name clone
    var author_name = quote_post.getElement('.forum_topic_posts_author_name').clone();

    // Get post body clone
    var quote_body = quote_post.getElement('div.forum_topic_posts_info_body').clone();

    //quote_body.set('class', 'forum_post_reply_quote');



    // Prepare containers
    this.initQuoteElements();
    this.quoteReplying.strong.innerHTML = author_name.innerHTML + ' said:';
    this.quoteReplying.strong.inject(this.quoteReplying.blockquote);
    this.quoteReplying.blockquote.innerHTML = this.quoteReplying.blockquote.innerHTML + '<br />' + quote_body.innerHTML;
    this.quoteReplying.blockquote.inject(this.quoteReplying.quote_div);
    this.quoteReplying.quote_div.inject(this.quoteReplying.quote_grab);
    this.quoteReplying.quote_grab.inject(form.getElement('.form-elements'), 'before');
    this.quoteReplying.quote_arrow.inject(this.quoteReplying.quote_grab, 'after');

    // Prepare textarea
    var body_textarea = form.getElement('#body');

    var body_textarea_copy = body_textarea.clone();

    body_textarea_copy.set('value', '');
    body_textarea_copy.setStyle('display', 'block');
    body_textarea.setStyle('display', 'none');
    body_textarea.set('value', this.quoteReplying.quote_div.innerHTML);
    body_textarea_copy.set('id', 'body_c');
    body_textarea_copy.set('name', 'body_c');
    body_textarea_copy.setStyle('width', '90%');
    body_textarea.setStyle('width', '90%');
    body_textarea_copy.inject(body_textarea, 'after');

    form.getElement('h3').set('text', 'Post Reply');

    // Close button
    this.quoteReplying.close_quoting.set('text', 'X');
    this.quoteReplying.close_quoting.inject(this.quoteReplying.quote_grab, 'top');

    // Bind event

    this.quoteReplying.close_quoting.addEvent("mousedown", function(ev){ self.quoteReplying.close_quoting.addClass('close_quoting_touchstart');});
    this.quoteReplying.close_quoting.addEvent("mouseup", function(ev){ self.quoteReplying.close_quoting.removeClass('close_quoting_touchstart');});
    var destroy =
      function(ev){
        self.quoteReplying.close_quoting.removeClass('close_quoting_touchstart');
        body_textarea.set('value', body_textarea_copy.get('value'));
        body_textarea.setStyle('display', 'block');
        self.quoteReplying.quote_arrow.dispose();
        self.quoteReplying.close_quoting.removeEvent("click", destroy);
        body_textarea_copy.dispose();
        self.quoteReplying.quote_grab.dispose();
      };
    this.quoteReplying.close_quoting.addEvent("click", destroy);

    
    // Bind event
    body_textarea_copy.addEvent('change', function(ev){
      var event = new Event(ev);
      event.stop();
      body_textarea.set('value', self.quoteReplying.quote_div.innerHTML + body_textarea_copy.get('value'));

    });

    $('photo-wrapper').setStyle('display', 'block');
    this.scroll.toElement(form);
  },

  quickReply: function(){
    var self = this;
    var form = $('forum_post_quick');
    if(form.getStyle('display') == 'block')
      form.setStyle('display', 'none');
    else{
      form.setStyle('display', 'block');
      form.getElement('#body').setStyle('width', '90%');
    }
    this.scroll.toElement(form);

  },
  formatQuote: function(elem){
    

  }
});

var Watch = new Class({

})