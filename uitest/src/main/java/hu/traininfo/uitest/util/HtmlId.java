package hu.traininfo.uitest.util;

public enum HtmlId {
    FROM_STATION("fromStation", "Honnan:"),
    TO_STATION("toStation", "Hova:"),
    SEARCH_BTN("searchBtn", "Menetrend"),
    MAIN_TITLE("mainTitle", "main title"),
    SEARCH_LNK("searchLink", "Új keresés");

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