/// <reference path="/var/www/sell-mmo.loc/typings/index.d.ts" />
declare var Translator: any;
abstract class Ticket {
    grid: string;
    form: string;

    constructor(grid) {
        this.grid = grid;
        this.form = "#" + grid + "_search";
    }
}
class TicketSelect2 extends Ticket {
    constructor(grid: string) {
        super(grid);
    }

    public static  convertEmptyValue(evt) {
        let $el = $(evt.target);
        if (! $el.val()) {
            $el.val("");
        }
    }

    protected  submitSelect(evt) {
        TicketSelect2.convertEmptyValue(evt);
        $(this.form).submit();
    }

    public  initSelect2Adg(JQselector, options?) {
        let settings = $.extend({
            // default settings
            allowClear: true,
            theme: "bootstrap"
        }, options);
        return JQselector.select2({
            theme: settings.theme,
            allowClear: settings.allowClear
        }).on("change", this.submitSelect);
    };
}
class TicketDatePicker extends Ticket {
    constructor(grid: string) {
        super(grid);
        this.form = "#" + grid + "_search";
    }

    private submitDate(ev, picker) {
        let $inputFrom = $(ev.target);
        let $inputTo = $("#grid_ticket__createdAt__query__to");
        $inputTo.val(picker.endDate.format("YYYY-MM-DD 23:59:59"));
        $inputFrom.val(picker.startDate.format("YYYY-MM-DD 00:00:00"));
        console.log($inputTo.val());
        console.log($inputFrom.val());
        $(this.form).submit();
    }

    public initDatePickerAdg(JQselector?) {
        let today = "some day";
        let settings = {
            // default settings
            ranges: {},
            dateLimit: {
                days: "60"
            } ,
            locale: {
                format: "YYYY-MM-DD",
            },
            opens: "left"
        };
        // Translations with Bazinga Translate Bundle
        settings.ranges[Translator.trans("ticket.date.today")] = [moment(), moment()];
        settings.ranges[Translator.trans("ticket.date.yesterday")] = [moment().subtract(1, "days"), moment().subtract(1, "days")];
        settings.ranges[Translator.trans("ticket.date.last7days")] = [moment().subtract(6, "days"), moment()];
        settings.ranges[Translator.trans("ticket.date.last30days")] = [moment().subtract(29, "days"), moment()];
        settings.ranges[Translator.trans("ticket.date.thismonth")] = [moment().startOf("month"), moment().endOf("month")];
        settings.ranges[Translator.trans("ticket.date.lastmonth")] = [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")];
        settings.locale["customRangeLabel"] = Translator.trans("ticket.date.custom");
        settings.locale["applyLabel"] = Translator.trans("ticket.date.label.apply");
        settings.locale["cancelLabel"] = Translator.trans("ticket.date.label.cancel");
        return JQselector.daterangepicker({
            ranges: settings.ranges,
            locale: settings.locale,
            opens: settings.opens
        }).on("apply.daterangepicker", this.submitDate);
    }
}
class TicketPriority {
    submitBtnId: JQuery;
    selectId: JQuery;
    priorityClass: JQuery;

    constructor(submitBtnId: JQuery, selectId: JQuery, priorityClass: JQuery) {
        this.submitBtnId = submitBtnId;
        this.selectId = selectId;
        this.priorityClass = priorityClass;
    }

    public checkPriority() {
        let priority = this.priorityClass;
        let selectPriority = this.selectId;
        let submitBtn = this.submitBtnId;
        submitBtn.prop("disabled", "disabled");
        selectPriority.on("change", function (e) {
            let current = selectPriority.children("option").filter(":selected").text().trim();

            if (current === priority.text().trim()) {
                submitBtn.prop("disabled", "disabled");
            }
            else {
                submitBtn.prop("disabled", false);
            }
        });
    }

}
$(document).ready(function () {
    new TicketSelect2("grid_ticket").initSelect2Adg($(".ticket-filters"));
    new TicketDatePicker("grid_ticket").initDatePickerAdg($("#grid_ticket__createdAt__query__from"));
    new TicketPriority($("#Message_changePriority"), $("#Message_priority"), $(".ticket-label-priority")).checkPriority();
    $("select").select2({
        theme:"bootstrap"
    })
});

