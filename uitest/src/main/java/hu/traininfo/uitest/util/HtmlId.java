package hu.traininfo.uitest.util;

public enum HtmlId {
    FROM_STATION("fromStation", "Honnan:"),
    TO_STATION("toStation", "Hova:"),
    VIA_STATION("viaStation", "Érintve:"),
    SEARCH_BTN("searchBtn", "Menetrend"),
    MAIN_TITLE("mainTitle", "main title"),
    FROM_STATION_LNK("fromStationLink", "initial station"),
    TO_STATION_LNK("toStationLink", "final station"),
    STATION_LNK("stationLink", "station"),
    TRAIN_LNK("trainLink", "train"),
    TRIPINFO_LNK("tripinfoLink", "Részletek"),
    TIMETABLE_LNK("timetableLink", "Vissza az utak listájához"),
    SEARCH_LNK("searchLink", "Új keresés"),
    ADD_FAVOURITE_CHECKBOX("isFavourite", "Kerüljön a kedvencek közé:"),
    DELETE_FAVOURITE_BTN("delete", "Törlés");

    private static final String UNKNOWN_HTML_ELEMENT_TEXT = "Unknown HTML element text: %s";

    private String htmlId;
    private String htmlText;

    private HtmlId(String htmlLabelId, String htmlLabelText) {
        this.htmlId = htmlLabelId;
        this.htmlText = htmlLabelText;
    }

    public static String getHtmlLabelIdForLabelText(String htmlLabelText) {
        for (HtmlId htmlId : HtmlId.values()) {
            if (htmlId.htmlText.equals(htmlLabelText)) {
                return htmlId.htmlId;
            }
        }
        throw new RuntimeException(String.format(UNKNOWN_HTML_ELEMENT_TEXT, htmlLabelText));
    }
}