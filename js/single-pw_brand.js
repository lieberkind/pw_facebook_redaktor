var PERSONA = (function($) {
  var self = this;

  self.initialized = false;

  self.brandId; self.personId; self.personName;
  self.updates; self.container; self.personImage;

  var API = {};

  API.init = function(brandId, containerElement, callback) {
    if(!self.initialized) {
      self.initialized = true;
      self.brandId = brandId;
      self.container = containerElement;
      API.update(function() {
        API.render();
        typeof callback === 'function' && callback();
      });
    }
  };

  API.update = function(callback) {
    var postData = { action: "getRandomPerson", brand_id: brandId };
    $.post('wp-admin/admin-ajax.php', postData, function(data) {
      var res = $.parseJSON(data);
      self.personId = res.id;
      self.personName = res.name;
      self.updates = res.updates;
      self.personImage = res.image;

      typeof callback === 'function' && callback();
    });
  }

  API.getId = function() {
    return self.personId;
  }

  API.getName = function() {
    return self.personName;
  }

  API.getUpdates = function() {
    return self.updates;
  }

  API.getImage = function() {
    return self.personImage;
  }

  API.render = function() {

    var html = "";
    html += '<header class="persona-header">';
    html += '<p class="persona-name">' + self.personName + ' skal finde din update interessant</p>';
    html += '</header>';
    html += '<div class="persona-updates">';
    for(i in self.updates) {
      html += '<div class="persona-update">';
      html += '<img src="' + self.personImage + '" class="persona-image" />';
      html += '<p class="update-persona-name">' + self.personName + '</p>';
      html += '<p class="update-persona-text">' + self.updates[i] + '</p>';
      html += '</div>';
    }
    html += '</div>';

    self.container.append(html);
  };

  return API;

}(jQuery));


var INSPIRATION = (function($) {
  var self = this;

  self.personId; self.inspirationImage; self.thinkWord;
  self.sayWord; self.doWord; self.ownWord, self.container;

  var API = {};

  API.init = function(brandId, container, personId) {
    self.container = container;
    self.personId = personId;
    self.brandId = brandId;
    API.update(function() {
      API.render();
    });
  };

  API.update = function(callback) {
    var postData = { action: "getRandomInspiration", person_id: self.personId };
    $.post('wp-admin/admin-ajax.php', postData, function(data) {
      var res = $.parseJSON(data);

      self.sayWord = res.i_say_word;
      self.doWord = res.i_do_word;
      self.thinkWord = res.i_think_word;
      self.ownWord = res.i_own_word;
      self.inspirationImage = res.image_url;
      
      typeof callback === 'function' && callback();
      console.log(res);
    });
  };

  API.getSayWord = function() {
    return self.sayWord;
  }

  API.getDoWord = function() {
    return self.doWord;
  }

  API.getThinkWord = function() {
    return self.thinkWord;
  }

  API.getOwnWord = function() {
    return self.ownWord;
  }

  API.getInspirationImage = function() {
    return self.inspirationImage;
  }

  API.render = function(callback) {
    $("#inspiration-image", container).attr('src', self.inspirationImage);

    $("#say-word", container).text(self.sayWord);
    $("#do-word", container).text(self.doWord);
    $("#think-word", container).text(self.thinkWord);
    $("#own-word", container).text(self.ownWord);


    container.append()

    // Run callback function if it has been defined
    typeof callback === 'function' && callback();
  };

  return API;

} (jQuery));














