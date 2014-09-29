function ajaxChangeCategoryPage(sender) {
    var $this = $(sender), category = $this.data('category');
    $('#category-ajax-' + category).load(
        URL_ROOT + 'MPosts/Category' + URL_SUFFIX, 
        'category=' + category + '&in_template=1&ajax_pages=1&count=' + 
                $this.data('count') + '&page=' + $this.text()
    );
    return false;
}