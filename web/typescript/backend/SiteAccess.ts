class SiteAccess {
    private $adminForm: JQuery;

    constructor() {
        this.initSite();
        this.initUsers();
        this.initAdmins();
    }

    private initSite() {
        let $siteForm = $("#siteSettingsForm");
        $siteForm.find(".loginMethods .namespaced input").change(function () {
            if ($(this).prop("checked")) {
                $("#accountsForm").removeClass("hidden");
            } else {
                $("#accountsForm").addClass("hidden");
            }
        }).trigger("change");

        $(".managedUserAccounts input").change(function () {
            if ($(this).prop("checked")) {
                $(".showManagedUsers").show();
            } else {
                $(".showManagedUsers").hide();
            }
        }).trigger("change");
    }

    private initUsers() {
        $("#accountsCreateForm").submit(function (ev) {
            let text = $("#emailText").val();
            if (text.indexOf("%ACCOUNT%") == -1) {
                bootbox.alert(__t("admin", "emailMissingCode"));
                ev.preventDefault();
            }
            if (text.indexOf("%LINK%") == -1) {
                bootbox.alert(__t("admin", "emailMissingLink"));
                ev.preventDefault();
            }

            let emails = $("#emailAddresses").val().split("\n"),
                names = $("#names").val().split("\n");
            if (emails.length == 1 && emails[0] == "") {
                ev.preventDefault();
                bootbox.alert(__t("admin", "emailMissingTo"));
            }
            if (emails.length != names.length) {
                bootbox.alert(__t("admin", "emailNumberMismatch"));
                ev.preventDefault();
            }
        });

        $('.accountListTable .accessViewCol input[type=checkbox]').change(function () {
            if (!$(this).prop("checked")) {
                $(this).parents('tr').first().find('.accessCreateCol input[type=checkbox]').prop('checked', false);
            }
        });
        $('.accountListTable .accessCreateCol input[type=checkbox]').change(function () {
            if ($(this).prop("checked")) {
                $(this).parents('tr').first().find('.accessViewCol input[type=checkbox]').prop('checked', true);
            }
        });
    }

    private initAdmins() {
        this.$adminForm = $("#adminForm");

        this.$adminForm.on('click', '.removeAdmin', (ev)  => {
            let $button = $(ev.target),
                $form = $(ev.target).parents("form").first();

            bootbox.confirm(__t("admin", "removeAdminConfirm"), function (result) {
                if (result) {
                    let id = $button.data("id");
                    $form.append('<input type="hidden" name="removeAdmin" value="' + id + '">');
                    $form.submit();
                }
            });
        });

        console.log(this.$adminForm.find('.adminCard .typeSite'));
        this.$adminForm.on('change', '.adminCard .typeSite input', (ev) => {
            let $card = $(ev.target).parents('.adminCard').first();
            if ($(ev.target).prop('checked')) {
                $card.find('.typeCon, .typeProposal').addClass('hidden');
            } else {
                $card.find('.typeCon, .typeProposal').removeClass('hidden');
            }
        });
        this.$adminForm.find('.adminCard .typeSite input').trigger('change');
    }
}

new SiteAccess();
