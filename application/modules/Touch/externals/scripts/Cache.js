/**
 * Created by Hire-Expert LLC.
 * Author: Ulan
 * Date: 02.12.11
 * Time: 16:42
 */ 
var Cache = new Class({
  err_templ: 'Cache Error: ',
  enabled: true,
  caching_feature: 'auto',
  min_lifetime: 3000,
  max_lifetime: 6000,
  old_params: null,
  app_env: null,
  active_theme: null,
  cached_theme: null,
  initialize:function(cache_params){
    this.app_env = document.getElementsByName('app_env')[0].get('content');
    var cache_options = JSON.decode(document.getElementsByName('cache_settings')[0].get('content'));
    this.active_theme = document.getElementsByName('active_theme')[0].get('content');
    this.cached_theme = Cookie.read('cached_theme');
    if(this.app_env == 'development'){
      this.clearCache();
    };

    if(cache_params){
      if(cache_params.enabled){
        this.enabled = true;
      } else {
        this.enabled = false;
      }

      if($type(cache_params.caching_feature) == 'string'){
        this.caching_feature = cache_params.caching_feature;
      }

      if($type(cache_params.max_lifetime) == 'number' && $type(cache_params.min_lifetime) == 'number'){
        this.max_lifetime = cache_params.max_lifetime;
        this.min_lifetime = cache_params.min_lifetime;
      }
    } else {
      this.enabled = parseInt(cache_options.enabled);
      this.caching_feature = cache_options.caching_feature;
      this.min_lifetime = parseInt(cache_options.min_lifetime);
      this.max_lifetime = parseInt(cache_options.max_lifetime);
    }
    this.getCSS();
  },
  cachePage: function(params, page, storage_type, dontprecache){
    if(!this.enabled) return false;
    if(!dontprecache)
      this.precachePage(params, storage_type);
    var count = 1;
    var lifetime = this.min_lifetime;
    var now_SEC = this.getTimeSEC();
    var old_cache = this.getPage(params, storage_type, true, true);
    var c_interval;
    if(old_cache && this.max_lifetime > (c_interval = now_SEC - old_cache.date)){
      count = old_cache.cache_count;
      if(old_cache.body.length != page.body.length)
        count++;
      lifetime = Math.round((old_cache.lifetime * old_cache.cache_count + c_interval)/count);
    }
    lifetime = lifetime < this.min_lifetime ? this.min_lifetime : lifetime;
    lifetime = lifetime > this.max_lifetime ? this.max_lifetime : lifetime;

    var key = this.getKey(params);
    var page_params = {
      date: now_SEC,
      lifetime: lifetime,
      cache_count: count//, We temporarily removed
      //params: params      This option
    }
    try {
      if(dontprecache)
        console.log(page.js);
      _StorageAPI.setObjectItem(key, page_params, storage_type);
      _StorageAPI.setItem(key, page.body, storage_type);
      _StorageAPI.setItem(key + 'js', page.js, storage_type);
    } catch (e){
      console.log(this.err_templ + ' Page "'+params.url+'" can not be cached');
    }
  },
  getPage: function(params, storage_type, getanyway, dontprecache){
    var page = _StorageAPI.getObjectItem(this.getKey(params), storage_type);
    var page_body = _StorageAPI.getItem(this.getKey(params), storage_type);
    var page_js = _StorageAPI.getItem(this.getKey(params) +'js', storage_type);
    var refresh = false;
    if(
      !this.enabled ||
      page == null
    )
      return false;
    else if(
      !getanyway &&
      page.lifetime < this.getTimeSEC() - page.date
    ){
       refresh = true;
    }
    page.body = page_body;
    page.js = page_js;
    if(refresh && !dontprecache)
      this.precachePage(params, storage_type, refresh);
    return page;
  },

  getKey: function(params){
    var key = params.url + '_' + JSON.encode(params.data);
    if(key.length > 255)
      key = key.substr(0, 255);
    key = key.replace('#', '_').replace('&', '_').replace('\\', '_').replace('/', '_');
    //console.log(key);
    return key;
  },
  getTimeSEC: function(){
    return Math.round((new Date()).getTime()/1000);
  },
  precachePage: function(params, storage_type, refresh){
    if(!this.enabled)
      return
    var self =this;
    setTimeout(function(){
      var old_params =self.old_params;
      if(old_params){
        var r = new Request.HTML({
          url: old_params.url,
          method: old_params.method,
          data: old_params.data,
          evalScripts : false,
          onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            var el = new Element('div', {html:responseHTML});
            var page = {
              js: responseJavaScript,
              body: null
            }
            var respContent = el.getElement('.content_global');
            if(respContent){
              responseHTML = respContent.innerHTML;
            }
            page.body = responseHTML;
            self.cachePage(old_params, page, storage_type, true);
            if(refresh && Touch)
              Touch.refresh(true);
          }
        });

        r.send();
      }
      self.old_params = params;
    }, 0);
  },
  cacheCSS: function(){
    Cookie.write('css_cached', 'false');
    if(
      ((navigator.userAgent.match(/Opera Mini/i)) || (navigator.userAgent.match(/Opera Mobi/i)) || (navigator.userAgent.match(/opera mini/i)) || (navigator.userAgent.match(/opera mobi/i))) ||
      ((navigator.userAgent.match(/blackberry/i)) || (navigator.userAgent.match(/Blackberry/i)) || (navigator.userAgent.match(/BlackBerry/i)))
    )
      return;
    if(localStorage && $(document.head).getElement('style').innerHTML.length> 100000){
        var originalLength = $(document.head).getElement('style').innerHTML.length;
      localStorage.setItem('css_cached', $(document.head).getElement('style').innerHTML);
        var cachedLength = localStorage.getItem('css_cached').length;
        if(originalLength == cachedLength)
            Cookie.write('css_cached', 'true');
      Cookie.write('cached_theme', this.active_theme);
    }
  },
  getCSS: function(){
    if(
        Cookie.read('css_cached')=='true' &&
        this.cached_theme == this.active_theme &&
        localStorage.getItem('css_cached').length> 100000
      ){
      $(document.head).getElement('style').set('html', localStorage.getItem('css_cached'));
    } else if(this.enabled){
      this.cacheCSS();
    }
  },
  clearCache: function(){
    _StorageAPI.clearStorage('session', true);
    this.clearCSSCache();
  },
  clearCSSCache: function(){
    Cookie.write('css_cached', 'false');
    if(localStorage)
      localStorage.clear();
  }
});
const _Cache = new Cache();