var fsCMS = {
  WaitAnimation: function(Action, WhereId, Width, Image) {
    Image = Image || URL_IMG + 'ajax-loader.gif';
    var autoId = 'fs-ajax-loader-spinner';
    Width = Width || 64;
    WhereId = WhereId || autoId;
    if (WhereId === '' || WhereId === true) {
      WhereId = autoId;
    } 
    var o = document.getElementById(WhereId);
    var newObj = WhereId === autoId;
    if (o === null) {
      o = document.createElement('div');
      o.setAttribute('id', autoId);
      o.setAttribute('class', autoId);
      o.style.display = 'none';
      o.style.position = 'fixed';
      o.style.background = 'rgba(0,0,0,0.5)';
      o.style.top = 0;
      o.style.left = 0;
      o.style.width = '100%';
      o.style.height = '100%';
      document.body.appendChild(o);
    }
    switch (Action.toLowerCase()) {
      case 'on':
        var imgStyle = newObj ? "left:50%;top:50%;position:fixed;margin-left:-"+(Width/2)+"px;margin-top:-"+(Width/2)+"px;" : '';
        o.innerHTML = "<img style='"+imgStyle+"' src='" + Image + "' alt='...' width='"+Width+"' title='...' border='0' />";
        if (newObj) {
          o.style.display = 'block';
        }
        break;
      case 'off':
        o.innerHTML = '';
        if (WhereId === autoId) {
          o.style.display = 'none';
        }
        break;
      default:
        break;
    }
  },
  Ajax: function(Url, Method, StrData, ResultId, WaitAnimationId, WaitAnimationWidth, CallBack, CallBackError, CallBackComplete) {
    if (ResultId) {
        ResultId = ResultId.split(',') || false;
    }
    WaitAnimationId = WaitAnimationId || false;
    WaitAnimationWidth = WaitAnimationWidth || false;
    CallBack = CallBack || false;
    CallBackComplete = CallBackComplete || false;
    CallBackError = CallBackError || false;
    if (WaitAnimationId !== false) {
      this.WaitAnimation('On', WaitAnimationId, WaitAnimationWidth);
    }
    $.ajax({
      url: Url, type: Method, data: StrData,
      error: function (XMLHttpRequest, textStatus, errorThrown) {
          if (typeof (CallBackError) === 'function') {
              CallBackError(XMLHttpRequest, textStatus, errorThrown);
          }
      },
      complete: function (jqXHR, status) {
          if (WaitAnimationId !== false && (ResultId === false || ResultId.indexOf(WaitAnimationId) === -1)) {
              fsCMS.WaitAnimation('Off', WaitAnimationId);
          }
          if (typeof (CallBackComplete) === 'function') {
              CallBackComplete(jqXHR, status);
          }
      },
      success: function(answer) {
          if (ResultId) {
            for(var i = 0; i < ResultId.length; ++i) {
              $("#"+ResultId[i]).html(answer);
            }
          }
          if (typeof(CallBack) === 'function') {
            CallBack(answer);
          }
        }  
    });
  },
  Chpu: function(str, id, space, disallow) {
    id = id || false;
    space = space || '-';
    disallow = disallow || [];
    str = str.replace(/а/g, 'a').replace(/б/g, 'b').replace(/в/g, 'v').replace(/г/g, 'g');
    str = str.replace(/д/g, 'd').replace(/е/g, 'e').replace(/ё/g, 'e').replace(/ж/g, 'j');
    str = str.replace(/з/g, 'z').replace(/и/g, 'i').replace(/й/g, 'i').replace(/к/g, 'k');
    str = str.replace(/л/g, 'l').replace(/м/g, 'm').replace(/н/g, 'n').replace(/о/g, 'o');
    str = str.replace(/п/g, 'p').replace(/р/g, 'r').replace(/с/g, 's').replace(/т/g, 't');
    str = str.replace(/у/g, 'y').replace(/ф/g, 'f').replace(/х/g, 'x').replace(/ц/g, 'c');
    str = str.replace(/ч/g, 'ch').replace(/ш/g, 'sh').replace(/щ/g, 'sh').replace(/ъ/g, '');
    str = str.replace(/ы/g, 'i').replace(/ь/g, '').replace(/э/g, 'e').replace(/ю/g, 'u').replace(/я/g,'ya');
    str = str.replace(/А/g, 'A').replace(/Б/g, 'B').replace(/В/g, 'B').replace(/Г/g, 'G');
    str = str.replace(/Д/g, 'D').replace(/Е/g, 'E').replace(/Ё/g, 'E').replace(/Ж/g, 'J');
    str = str.replace(/З/g, 'Z').replace(/И/g, 'I').replace(/Й/g, 'I').replace(/К/g,'K');
    str = str.replace(/Л/g,'L').replace(/М/g,'M').replace(/Н/g,'N').replace(/О/g,'O').replace(/П/g,'P');
    str = str.replace(/Р/g,'R').replace(/С/g,'S').replace(/Т/g,'T').replace(/У/g,'Y').replace(/Ф/g,'F');
    str = str.replace(/Х/g,'X').replace(/Ц/g,'C').replace(/Ч/g,'CH').replace(/Ш/g,'SH');
    str = str.replace(/Щ/g,'SH').replace(/Ъ/g,'').replace(/Ы/g,'I').replace(/Ь/g,'').replace(/Э/g,'E').replace(/Ю/g,'U').replace(/Я/g,'YA');
    str = str.replace(/\s/g, space).replace(/\//g, '_');
    str = str.replace(/[\*%&\\№\?#!:;\$\^,\(\)\[\]=\.]/g, '');
    for(var i = 0; i < disallow.length; ++i) {
        str = str.replace(new RegExp(disallow[i], 'g'), '');
    }
    if (id) {
      $('#'+id).val(str);
    }
    return str;
  },
  UpdateImage: function(imgId) {
    var p = 'fsCMSUpdate=', src = $('#' + imgId).attr('src'), delimiter = src.indexOf('?') < 0 ? '?' : '&';
    if(src.indexOf('?' + p) >= 0 || src.indexOf('&' + p) >= 0) {
        delimiter = '';
    }
    src = src.replace(new RegExp(p + '\\d+\\.\\d+$', 'gi'), '');
    $('#' + imgId).attr('src', src + delimiter + p + Math.random());
  },
  IsNumeric: function(obj, def, intOnly, positiveOnly) {
    def = def || 0;
    intOnly = intOnly || false;
    positiveOnly = positiveOnly || false;
    if (obj.value === '' || (obj.value === '-' && !positiveOnly)) {
      return;
    }
    if (!$.isNumeric(obj.value) ||
         (positiveOnly && obj.value < 0) ||
         (intOnly && !/^\-?(\d+)?$/.test(obj.value))
       ) {
      obj.value = def;  
    }
    var prefix = '';
    if (obj.value[0] === '-') {
      prefix = '-';
      obj.value = obj.value.substr(1);
    }
    while (obj.value.length > 1 && obj.value[0] === '0') {
      obj.value = obj.value.substr(1);  
    }    
    obj.value = prefix + obj.value;
  } 
};