package hu.traininfo.uitest.step;

import hu.traininfo.uitest.util.HtmlId;

import java.net.MalformedURLException;
import java.net.URL;
import java.util.concurrent.TimeUnit;

import org.jbehave.core.annotations.AfterStories;
import org.jbehave.core.annotations.Alias;
import org.jbehave.core.annotations.BeforeStories;
import org.jbehave.core.annotations.Given;
import org.jbehave.core.annotations.Then;
import org.jbehave.core.annotations.When;
import org.junit.Assert;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.remote.DesiredCapabilities;
import org.openqa.selenium.remote.RemoteWebDriver;
import org.openqa.selenium.remote.RemoteWebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import com.google.common.base.Function;

/**
 * Traininfo - Hungarian train timetable for Amazon Kindle eBook
 * 
 * Copyright (C) 2012-2022 Tamás Kifor
 * 
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see http://www.gnu.org/licenses/.
 * 
 * If you have any question contact to Tamás Kifor via email: tamas@kifor.hu
 * 
 * @author Tamás Kifor
 */
public class Steps {
    private static final int DEFAULT_TIMEOUT_IN_SECONDS = 10;
    // private static final String KINDLE_HTTP_USER_AGENT = "";
    private WebDriver browser;
    private String testEnv;

    @BeforeStories
    public void openBrowser() throws MalformedURLException {
        this.testEnv = System.getProperty("traininfo.test.env");
        if (testEnv == null || testEnv.trim().length() == 0 || testEnv.equalsIgnoreCase("prod")) {
            testEnv = "www";
        }

        browser = new RemoteWebDriver(new URL("http://127.0.0.1:4444/wd/hub"), DesiredCapabilities.firefox());
        browser.manage().timeouts().implicitlyWait(DEFAULT_TIMEOUT_IN_SECONDS, TimeUnit.SECONDS);

        // FirefoxProfile profile = new FirefoxProfile();
        // profile.addAdditionalPreference("general.useragent.override",
        // KINDLE_HTTP_USER_AGENT);
        // WebDriver driver = new FirefoxDriver(profile);
    }

    @Given("that I navigate to $webpage")
    public void navigateTo(String webpage) {
        browser.get(getWebpageUrl(webpage));
    }

    @When("I enter $inputValue to field labelled $labelText")
    public void enterTextIntoField(String inputValue, String labelText) {
        WebElement inputField = browser.findElement(By.id(HtmlId.getHtmlLabelIdForLabelText(labelText)));
        inputField.clear();
        inputField.sendKeys(inputValue);
    }

    @When("I click on $buttonText button")
    @Alias("I click on $linkText link")
    public void click(String buttonOrLinkText) {
        browser.findElement(By.id(HtmlId.getHtmlLabelIdForLabelText(buttonOrLinkText))).click();
    }

    @When("$expectedPage appeared")
    public void whenPageLoaded(final String webpage) {
        checkPageLoad(webpage, DEFAULT_TIMEOUT_IN_SECONDS);
    }

    @Then("I should see $expectedPage page within $timeout seconds")
    public void checkPageLoad(final String webpage, Integer timeout) {
        final String webpageUrl = getWebpageUrl(webpage);
        if (timeout == null) {
            timeout = DEFAULT_TIMEOUT_IN_SECONDS;
        }
        WebDriverWait webDriverWait = new WebDriverWait(browser, timeout);
        webDriverWait.until(new Function<WebDriver, WebElement>() {
            // @Override
            public WebElement apply(WebDriver driver) {
                boolean expectedUrl = driver.getCurrentUrl().contains(webpageUrl);
                if (expectedUrl) {
                    return new RemoteWebElement();
                }
                return null;
            }
        });
    }

    @Then("I should see $expectedFieldValue in field labelled $humanReadableFieldName")
    public void checkFieldValue(String expectedFieldValue, String humanReadableFieldName) {
        WebElement field = browser.findElement(By.id(HtmlId.getHtmlLabelIdForLabelText(humanReadableFieldName)));
        String actualFieldValue = field.getAttribute("value");

        Assert.assertEquals(humanReadableFieldName, expectedFieldValue, actualFieldValue);
    }

    @Then("I should see $expectedFieldText title in the $humanReadableFieldName field within $timeout seconds")
    public void checkFieldText(String expectedFieldText, String humanReadableFieldName, Integer timeout) {
        if (timeout == null) {
            timeout = DEFAULT_TIMEOUT_IN_SECONDS;
        }
        WebDriverWait wait = new WebDriverWait(browser, timeout);
        WebElement field = wait.until(ExpectedConditions.visibilityOfElementLocated(By.id(HtmlId.getHtmlLabelIdForLabelText(humanReadableFieldName))));
        String actualFieldText = field.getText();

        Assert.assertEquals(humanReadableFieldName, expectedFieldText, actualFieldText);
    }

    @Then("I should see the traininfo version under testing")
    public void checkVersion() {
        Assert.assertEquals("version", System.getProperty("traininfo.version"), browser.findElement(By.tagName("body")).getText());
    }

    @AfterStories
    public void closeBrowser() {
        if (browser != null) {
            browser.quit();
        }
    }

    private String getWebpageUrl(String webpage) {
        String webpageUrl = webpage != null ? webpage : "";
        if (!webpageUrl.startsWith("http")) {
            if (webpageUrl.startsWith("vonatinfo.hu")) {
                webpageUrl = "http://" + testEnv + "." + webpageUrl;
            } else {
                if (!webpageUrl.startsWith("/")) {
                    webpageUrl = "/" + webpageUrl;
                }
                webpageUrl = "http://" + testEnv + ".vonatinfo.hu" + webpageUrl;
            }
        }
        return webpageUrl;
    }
}
