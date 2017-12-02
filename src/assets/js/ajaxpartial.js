var ajaxPartial;

function AjaxPartial(ajaxPartialOptions) {
    var selectors = ajaxPartialOptions.selectors;
    var modal = ajaxPartialOptions.modal;

    function loadPartials(loadSelectors) {
        loadSelectors.forEach(function (element) {
            reloadElement(element);
        });
    }

    function addModal() {
        $('body').append(modal.template);
    }

    function init() {
        addClickListeners(selectors.clickSelectors, true);
        loadPartials(selectors.loadSelectors);
        addFormListeners(selectors.formSelectors, true);
        addModal();
    }

    var sendAjax = function (element, elementHref) {
        var url = "";
        if (element.url) {
            url = element.url;
        } else {
            url = elementHref;
        }
        $.ajax({
            type: element.method ? element.method : 'GET',
            url: url
        }).done(function (data) {
            if (typeof element.afterSend === 'function') {
                element.afterSend(data);
            }
            element.reload.forEach(function (reloadItem) {
                reloadElement(reloadItem, elementHref);
            });
        });
    };

    function addClickListeners(elements, skipCheck) {
        elements.forEach(function (clickElement) {
            if (skipCheck || !hasSelector(selectors.clickSelectors, clickElement.selector)) {
                $("body").on('click', "" + clickElement.selector, function (e) {
                    e.preventDefault();
                    var url = $(this).attr('href');
                    if (clickElement.sendAjax) {
                        sendAjax(clickElement, url);
                    }else{
                        clickElement.reload.forEach(function (element) {
                            reloadElement(element, url);
                        });
                    }
                });
            }
        });
    }

    function loadIntoContaier(element, url, params) {
        $('' + element.container)
            .empty()
            .addClass('loader');
        $.get({url: url, data: params}).done(function (response) {
            jQuery('' + element.container).removeClass('loader').html(response);
            if (typeof element.afterLoad === 'function') {
                element.afterLoad(response);
            }
        });
    }

    function loadIntoModal(element, url, params) {
        $("#" + modal.id).modal('show')
            .find('.modalContent')
            .empty()
            .load(url, params, function (response, status, xhr) {
                if (typeof element.aferModal === 'function') {
                    element.aferModal(response, status, xhr);
                }
            });
    }

    function reloadElement(element, url) {
        var url = element.useElementHref ? url : element.url;
        var params = (element.dynamicParams && (typeof element.dynamicParams === 'function')) ? element.dynamicParams() : {};
        if (element.container) {
            loadIntoContaier(element, url, params);
        } else {
            loadIntoModal(element, url, params);
        }
    }

    function hasSelector(allSelectors, selector) {
        var filteredSelectors = allSelectors.filter(function (formSelector) {
            return formSelector.selector === selector;
        });
        return filteredSelectors.length > 0;
    }

    function addFormListeners(elements, skipCheck) {
        elements.forEach(function (formSelector) {
            if (skipCheck || !hasSelector(selectors.formSelectors, formSelector.selector)) {
                $("body").on('beforeSubmit', "" + formSelector.selector, function (e) {
                    e.preventDefault();
                    var form = $(this);
                    if (typeof formSelector.onSubmit === 'function') {
                        formSelector.onSubmit();
                    }
                    $.ajax({
                            type: form.attr('method'),
                            url: form.attr('action'),
                            data: form.serializeArray()
                        }
                    ).done(function (data) {
                        formSelector.reload.forEach(function (element) {
                            reloadElement(element, formSelector.selector);
                        });
                        if (typeof formSelector.afterSubmit === 'function') {
                            formSelector.afterSubmit(data);
                        }
                    });
                    return false;
                });
            }
        });
    }

    this.addClick = function (newSelectors) {
        addClickListeners(newSelectors);
        selectors.clickSelectors = selectors.clickSelectors.concat(newSelectors);
    };
    this.addLoad = function (newSelectors) {
        loadPartials(newSelectors);
        selectors.loadSelectors = selectors.loadSelectors.concat(newSelectors);
    };
    this.addAjaxForm = function (newSelectors) {
        addFormListeners(newSelectors);
        selectors.formSelectors = selectors.formSelectors.concat(newSelectors);
    };
    this.closeModal = function () {
        $("#" + modal.id).modal('hide');
    };
    this.openModal = function () {
        $("#" + modal.id).modal('show');
    };
    init();
}