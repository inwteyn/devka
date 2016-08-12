/**
 * Created By: Hire-Experts LLC
 * Author: Ulan
 * Date: 30.11.11
 * Time: 17:28
 */

var StorageAPI = new Class({
  err_templ: 'Storage API Error: ',
  not_impl: ' is not implemented yet.',
  pref_setting : 'setting',
  pref_item : 'item',
  pref_object : 'object',
  pref_HTML : 'HTML',
  pref_JSON : 'JSON',
  pref_CSS : 'CSS',
  ppref : '~~',
  ppof : '##',
  js_storage:{},
  encodeKey: function(prefix, key){
    var pref =this.ppref + prefix + this.ppof;
    var i=0;
    if(key.substr(0, this.ppref.length) == this.ppref){
      i = key.search(this.ppof);
      pref = this.ppref + prefix;
    }
    return key.substr(0, i) + pref + key.substr(i);
  },
  compareObjects: function(x, y){
    return x.toSource() == y.toSource() ? true : false;
  },
  findMismatch : function(x, y){
    var a,b;
    if($type(x) == 'object' && $type(y) == 'object'){
      a = x.toSource();
      b = y.toSource();
    } else if($type(x) == 'string' && $type(y) == 'string'){
      a = x;
      b = y;
    } else
      throw new Error('invalid parameters');
    var min_len = a.length > b.length?b.length:a.length;
    var i;

    for(i = 0; i < min_len; i++){
      if(a.charAt(i) !== b.charAt(i)){
        break;
      }
    }
    if(min_len > i){
      return i;
    }
  },
  getSetting : function(key, storage_type){
    var storage = this.getStorage(storage_type);
    if(storage && $type(key) == 'string'){
      return storage.getItem(this.encodeKey(this.pref_setting, key));
    } else {
      return null;
    }
  },
  removeSetting : function(key, storage_type){
    var storage = this.getStorage(storage_type);
    if(storage && $type(key) == 'string'){
      storage.removeItem(this.encodeKey(this.pref_setting, key));
      return true;
    } else {
      return null;
    }
  },

  setSetting : function(key, value, storage_type, skip){
    var storage = this.getStorage(storage_type);
    if(storage){
      try{
        if(this.getSetting(key, storage_type) && skip)
          return true;
        else
          storage.setItem(this.encodeKey(this.pref_setting, key), value);
        return true;
      } catch (e){
        throw e;
      }
    } else {
      return false;
    }
  },

  getLocalSetting : function(key){
    return this.getSetting(key, 'local');
  },

  removeLocalSetting : function(key){
    return this.removeSetting(key, 'local');
  },

  setLocalSetting : function(key, value, skip){
    return this.setSetting(key, value, 'local', skip);
  },

  getSessionSetting : function(key){
    return this.getSetting(key, 'session');
  },

  removeSessionSetting : function(key){
    return this.removeSetting(key, 'session');
  },

  setSessionSetting : function(key, value, skip){
    return this.setSetting(key, value, 'session', skip);
  },

  getStorage : function(storage_type){

    if(localStorage && storage_type == 'local'){
      return localStorage;
    } else if(sessionStorage){
      return sessionStorage;
    } else {
      return this.js_storage;
    }
  },

  isLocalStorageSupported : function(){
    if(localStorage === undefined)
      return false;
    else
      return true;
  },

  isSessionStorageSupported : function(){
    if(sessionStorage === undefined)
      return false;
    else
      return true;
  },

  setItem:function(key, value, storage_type, skip){
    var storage = this.getStorage(storage_type);
    if(storage){
      try{
        if(this.getSetting(key, storage_type) && skip)
          return true;
        else
          storage.setItem(this.encodeKey(this.pref_item, key), value);
        return this.encodeKey(this.pref_item, key);
      } catch (e){
          return -1;
      }
    } else {
      return false;
    }
  },

  getItem:function(key, storage_type){
    var storage = this.getStorage(storage_type);
    if(storage){
      return storage.getItem(this.encodeKey(this.pref_item, key));
    } else {
      return null;
    }

  },

  removeItem : function(key, storage_type) {
    var storage = this.getStorage(storage_type);
    if(storage){
      storage.removeItem(this.encodeKey(this.pref_item, key));
      return true;
    } else {
      return null;
    }
  },

  removeJSONItem: function(key, storage_type){
    return this.removeItem(this.encodeKey(this.pref_JSON, key), storage_type);
  },

  removeObjectItem: function(key, storage_type){
    return this.removeJSONItem(this.encodeKey(this.pref_object, key), storage_type);
    
  },

  setHTMLItem:function(key, value, storage_type, skip){
    alert('setHTMLItem' + this.not_impl);
  },

  getHTMLItem:function(key, storage_type){
    alert('getHTMLItem' + this.not_impl);
  },

  setJSONItem:function(key, value, storage_type, skip){
    return this.setItem(this.encodeKey(this.pref_JSON, key), value, storage_type, skip);
  },



  getJSONItem:function(key, storage_type){
    return this.getItem(this.encodeKey(this.pref_JSON, key), storage_type);
  },

  setObjectItem:function(key, value, storage_type, skip){
    var json_string = JSON.encode(value);
    return this.setJSONItem(this.encodeKey(this.pref_object, key), json_string, storage_type, skip);
  },

  getObjectItem:function(key, storage_type){ 
    return JSON.decode(this.getJSONItem(this.encodeKey(this.pref_object, key), storage_type));
  },

  removeObjectItem:function(key, storage_type){
    return JSON.decode(this.getJSONItem(this.encodeKey(this.pref_object, key), storage_type));
  },
  setCSSItem:function(key, value, storage_type, skip){
    alert('getHTMLItem' + this.not_impl);
  },

  getCSSItem:function(key, storage_type){
    alert('getHTMLItem' + this.not_impl);
  },
  showAllRecords: function(storage_type){
    return this.getSpecifiedTypeItems(this.selectRecords(storage_type), '');
  },
  selectRecords: function(keyword, by_value, limit, storage_type){
    var storage = this.getStorage(storage_type);
    if(!keyword)
      return storage;
    var last = keyword.substr(0, 1)=='%' ? true : false;
    var first = keyword.substr(keyword.length-1, 1)=='%' ? true : false;
    var middle = first && last ? true : false;
    var select_result = {};
    console.log(first);
    console.log(last);
    console.log(middle);
    //limit = limit > 0 ? limit : Infinity;
    if(first)
      keyword = keyword.substr(0, keyword.length-1);
    if(last)
      keyword = keyword.substr(1);
    if(by_value){
        if(middle){
          for(var rec_key in storage){
            if(storage[rec_key].search(keyword) != -1)
              select_result[rec_key] = storage[rec_key];
          }
        } else if (first){
          for(var rec_key in storage){
            if(storage[rec_key].substr(0, keyword.length) == keyword)
              select_result[rec_key] = storage[rec_key];
          }
        } else if(last){
          for(var rec_key in storage){
            if(storage[rec_key].substr(storage[rec_key].length - keyword.length) == keyword)
              select_result[rec_key] = storage[rec_key];
          }
        } else {
          for(var rec_key in storage){
            if(storage[rec_key] == keyword)
              select_result[rec_key] = storage[rec_key];
          }
        }
    } else {
      if(middle){
        for(var rec_key in storage){
          if(rec_key.substr(rec_key.search(this.ppof)+this.ppof.length).search(keyword) != -1)
            select_result[rec_key] = storage[rec_key];
        }
      } else if (first){
        for(var rec_key in storage){
          if(rec_key.substr(rec_key.search(this.ppof)+this.ppof.length).substr(0, keyword.length) == keyword)
            select_result[rec_key] = storage[rec_key];
        }
      } else if(last){
        for(var rec_key in storage){
          if(rec_key.substr(rec_key.search(this.ppof)+this.ppof.length).substr(rec_key.length-keyword.length) == keyword)
            select_result[rec_key] = storage[rec_key];
        }
      } else {
        for(var rec_key in storage){
          if(rec_key.substr(rec_key.search(this.ppof)+this.ppof.length) == keyword)
            select_result[rec_key] = storage[rec_key];
        }
      }
    }
    return select_result;
  },
  selectRecordsByKey: function(keyword, limit, storage_type){
    return this.selectRecords(keyword, false, limit, storage_type)
  },
  selectRecordsByValue: function(keyword, limit, storage_type){
    return this.selectRecords(keyword, true, limit, storage_type)
  },

  selectItemRecords: function(keyword, by_value, limit, storage_type){
    var storage = this.selectRecords(keyword, by_value, limit, storage_type);
    return this.getSpecifiedTypeItems(storage, this.pref_item);
  },
  getSpecifiedTypeItems: function(storage, pref){
    pref = this.ppref + pref;
    var select_result ={};
    for(var rec_key in storage){
      if(rec_key.substr(0, pref.length) == pref)
        select_result[rec_key] = storage[rec_key];
    }
    return select_result;
  },
  selectJSONRecords: function(keyword, by_value, limit, storage_type){
    var storage = this.selectRecords(keyword, by_value, limit, storage_type);
    return this.getSpecifiedTypeItems(storage, this.pref_JSON);
  },
  selectObjectRecords: function(keyword, by_value, limit, storage_type){
    var storage = this.selectRecords(keyword, by_value, limit, storage_type);
    return this.getSpecifiedTypeItems(storage, this.pref_object);
  },
  selectHTMLRecords: function(keyword, by_value, limit, storage_type){
    var storage = this.selectRecords(keyword, by_value, limit, storage_type);
    return this.getSpecifiedTypeItems(storage, this.pref_HTML);
  },
  clearStorage: function(storage_type, silent){
    storage_type = (!storage_type || storage_type!= 'local') ? 'session': 'local';
    if(silent !== true){
      var clear = confirm('ATTENTION: This operation is completely REMOVE ALL RECORDS from ' + storage_type + 'Storage for '+window.location.hostname+' domain!!! Are you sure to execute?')
      console.log(clear);
    }
    if(silent !== true && clear == false)
      return;
    var storage = this.getStorage(storage_type);
    var len = 0;
    try {
      len = storage.length;
      storage.clear();
      if(silent !== true)
        alert(len + ' items removed from '+ storage_type+ 'Storage');
      else
        console.log(len + ' items removed from '+ storage_type+ 'Storage');
    } catch (e){
      if(silent !== true)
        alert(e.message());
      throw e;
    }

  }
});

// Global variable SrorageAPI class object
const _StorageAPI = new StorageAPI();
