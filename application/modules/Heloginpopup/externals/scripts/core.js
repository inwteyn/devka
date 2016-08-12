/* Id: core.js 20.09.13 12:26 TeaJay $ */
var loginPopup = {
    formUrl: '',
    forgotUrl: '',
    signupUrl: '',
    facebookUrl: '',
    twitterUrl: '',
    dateLimit: 30,
    content: '',
    modalView: '',
    return_url: '',

    init: function () {
        var self = this;

        self.content.inject($$('body')[0]);
        self.modalView.inject($$('body')[0]);

        var lastdate = localStorage.getItem('en4_heloginpopup_lastdate');
        var currentdate = new Date();

        if( !lastdate ) {
            self.showPopup();
            localStorage.setItem('en4_heloginpopup_lastdate',currentdate);
            return;
        }

        lastdate = new Date(lastdate);

        var dateDiff = parseInt((currentdate.getTime()-lastdate.getTime())/(24*3600*1000));

        console.log(dateDiff);

        if( dateDiff >= self.dateLimit ) {
            self.showPopup();
            localStorage.setItem('en4_heloginpopup_lastdate',currentdate);
        }
    },

    showPopup: function () {
        this.content.setStyle('display', 'block');
        this.modalView.setStyle('display', 'block');

        this.content.addClass('heloginpopup_in');
        this.modalView.addClass('heloginpopup_in');
    },

    hidePopup: function () {
        var self = this;
        this.content.removeClass('heloginpopup_in');
        this.modalView.removeClass('heloginpopup_in');
        (function () {
            self.modalView.setStyle('display', 'none');
            self.content.setStyle('display', 'none');
        }).delay(200);
    }
}

en4.core.runonce.add(function () {
    $$('.menu_core_mini.core_mini_auth').addEvent('click', function (e) {
        e.preventDefault();
        loginPopup.showPopup();
    });

});
