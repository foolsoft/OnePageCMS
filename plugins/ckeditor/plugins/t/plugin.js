var langT, translationsT = {
    ru: {
        title: 'Словари',
        texts: 'Тексты',
        words: 'Слова',
        constName: 'Имя константы',
        search: 'Поиск',
        value: 'Значение',
        clear: 'Очистить',
    }, en:
    {
        title: 'Dictionaries',
        texts: 'Texts',
        words: 'Words',
        constName: 'Constant name',
        search: 'Search',
        value: 'Value',
        clear: 'Clear',
    },
    def: 'en'
};

CKEDITOR.plugins.add( 't',
{
  _initTranslations: function( editor )
  {
      var current_lang = CKEDITOR.lang.detect();
      editor.lang['t'] = translationsT[ current_lang ]
          ? translationsT[ current_lang ]
          : translationsT[ translationsT.def ];
      langT = editor.lang.t;
  },

	init: function( editor )
	{
    this._initTranslations( editor );
    
    editor.addCommand( 'tDialog', new CKEDITOR.dialogCommand( 'tDialog' ) );
    
    editor.ui.addButton( 'Dictionaries',
    {
    	label: langT.title,
    	command: 'tDialog',
    	icon: this.path + 'images/icon.png'
    });
    		
	}
});

function CKEDITOR_t_LoadDictionary(dialog, callback) {
  callback = callback || false;
  dialog.getContentElement('tabXml', 'xml-dictionary').clear();
  CKEDITOR.ajax.load(URL_ROOT + 'AdminPanel/Dictionary' + URL_SUFFIX + '?substr=' + dialog.getContentElement('tabXml', 'xml-search').getValue(), function( data ) {
    var json = JSON.parse(data);
    for(var i = 0; i < json.data.length; ++i) {
      dialog.getContentElement('tabXml', 'xml-dictionary').add(json.data[i].text, json.data[i].value);
    }
    if(typeof(callback) == 'function') {
      callback(data);
    }
  });
}

function CKEDITOR_t_LoadTranslate(dialog, value) {
  value = value || dialog.getValueOf('tabXml', 'xml-dictionary');
  dialog.getContentElement('tabXml', 'xml-value').getElement().setHtml('...');
  CKEDITOR.ajax.load(URL_ROOT + 'Translate' + URL_SUFFIX + '?text=' + value, function( data ) {
    dialog.getContentElement('tabXml', 'xml-value').getElement().setHtml(data);
  });
}

CKEDITOR.dialog.add( 'tDialog', function ( editor )
{
	return {
		title : langT.title,
		height: 50,
    width: 400,
    onShow : function(e)
    {
      var dialog = this;
      CKEDITOR_t_LoadDictionary(e.sender, function(data) { CKEDITOR_t_LoadTranslate(dialog); });  
    },
    onOk: function() {
      var d = editor.document.createElement( 'span' );
      d.setText( '{T(' + this.getValueOf( 'tabXml', 'xml-dictionary' ) + ')}' );
      editor.insertElement( d );
    },
		contents:
		[
			{
				id : 'tabXml',
				label : langT.texts,
				elements :
				[
					{
            type: 'select',
            id: 'xml-dictionary',
            label: langT.constName,
            items: [], 'default': '',
              onChange: function(e) {
                CKEDITOR_t_LoadTranslate(e.sender.getDialog(), this.getValue());  
              }
          },
          {
            type : 'html', html : langT.search
          },
          {
            type: 'hbox',
            widths: [ '60%', '20%', '20%' ],
            children: [
                {
                    type: 'text',
                    id: 'xml-search',
                },
                {
                    type : 'button', id: 'xml-do-search', label: langT.search, onClick: function(e) {
                      CKEDITOR_t_LoadDictionary(e.sender.getDialog(), function(data) { CKEDITOR_t_LoadTranslate(e.sender.getDialog()); });  
                    }
                },
                {
                    type : 'button', id: 'xml-do-clear', label: langT.clear, onClick: function(e) {
                      e.sender.getDialog().getContentElement('tabXml', 'xml-search').setValue('');
                      CKEDITOR_t_LoadDictionary(e.sender.getDialog(), function(data) { CKEDITOR_t_LoadTranslate(e.sender.getDialog()); });  
                    }
                }
            ]

          },
          {
            type : 'html', id: 'xml-value', label: langT.value, html : '...', style: 'max-width:600px;display:block;'
          }
				]
			}
		]
	};
} );