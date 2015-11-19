var langDepoist, translationsD = {
    ru: {
        title: 'Загрузка на DepositFiles',
        upload: 'Загрузить'
    }, en:
    {
        title: 'Upload to depositfiles',
        upload: 'Upload'
    },
    def: 'en'
};

CKEDITOR.plugins.add( 'deposit',
{
  _initTranslations: function( editor )
  {
      var current_lang = CKEDITOR.lang.detect();
      editor.lang['deposit'] = translationsD[ current_lang ]
          ? translationsD[ current_lang ]
          : translationsD[ translationsD.def ];
      langDepoist = editor.lang.deposit;
  },

	init: function( editor )
	{
    this._initTranslations( editor );
    
    editor.addCommand( 'depositDialog', new CKEDITOR.dialogCommand( 'depositDialog' ) );
    
    editor.ui.addButton( 'Depositfiles',
    {
    	label: langDepoist.title,
    	command: 'depositDialog',
    	icon: this.path + 'images/icon.png'
    });
    		
	}
});

CKEDITOR.dialog.add( 'depositDialog', function ( editor )
{
	return {
		title : langDepoist.title,
		height: 50,
    width: 400,
    onShow : function()
    {
    	setTimeout(function() {
        var d = document.getElementById('ckdeposit-obje');
        d.style.width = '350px';
        d.style.height = '60px';
      }, 500);
    },
		contents:
		[
			{
				id : 'tab1',
				label : langDepoist.upload,
				elements :
				[
					{
            type : 'html',
	          html : '<div id="ckdeposit" style="text-align:center;">' + 
              '<object id="ckdeposit-obj" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="350" height="60">' +
              '<param name="movie" value="http://static.depositfiles.com/flash/DepositUploader_350x60.swf?ref=FooLsoft&member_passkey=ptpiehw03x0cmtk6&interfaceId=3&lang=RU&lang_xml=http%3A%2F%2Fstatic.depositfiles.com%2Fflash%2FDepositUploader.xml"></param>' +
              '<param name="menu" value="false"></param>' +
              '<param name="scale" value="noScale"></param>' + 
              '<param name="allowFullScreen" value="true"></param>' +
              '<param name="allowscriptaccess" value="always"></param>' +
              '<param name="wmode" value="transparent"></param>' +
              '<embed id="ckdeposit-obje" src="http://static.depositfiles.com/flash/DepositUploader_350x60.swf?ref=FooLsoft&member_passkey=ptpiehw03x0cmtk6&interfaceId=3&lang=EN&lang_xml=http%3A%2F%2Fstatic.depositfiles.com%2Fflash%2FDepositUploader.xml" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" menu="false" scale="noScale" wmode="transparent" width="350" height="60"></embed>' +
              '</object></div>'
          } 
				]
			},
		]
	};
} );