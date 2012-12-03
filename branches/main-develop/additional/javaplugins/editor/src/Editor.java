
/**
 * webEdition CMS
 *
 * This source is part of webEdition CMS. webEdition CMS is free software; you
 * can redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation; either version 3
 * of the License, or any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html. A copy is found in the textfile
 * license.txt
 *
 * @license http://www.gnu.org/copyleft/gpl.html GPL
 */
import java.awt.BorderLayout;
import java.awt.Dimension;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.StringReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.swing.JApplet;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.NodeList;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;

//
public class Editor extends JApplet {

	private static final long serialVersionUID = 1L;
	private EditorPanel editor;
	private ArrayList<String> tags = new ArrayList<String>();
	private String php_ext;
	public String serverUrl;
	public String editorPath;
	public String cmpCode = "";
	protected boolean isHot = false;
	private Parameter parameter;
	public boolean isCodeSet = false;
	private Map<String, ArrayList<String>> attribs = new HashMap<String, ArrayList<String>>();
//	private static String webPath="/webEdition/editor/"; // new: /editors/template/editor/
	private SearchAndReplace searchAndReplace;

	@Override
	public void init() {
		super.init();
		System.out.println("init");
		System.out.flush();
		String url;

		serverUrl = getParameter("serverUrl");
		editorPath = getParameter("editorPath");
		if (serverUrl != null && serverUrl.length() > 0) {
			if (!serverUrl.endsWith("/")) {
				serverUrl = serverUrl + "/";
			}
		}


		parameter = Parameter.getInstance();

		String p = getParameter("weTagColor");
		if (p != null && p.length() > 0) {
			parameter.setWeTagColor(p);
		}

		p = getParameter("weAttributeColor");
		if (p != null && p.length() > 0) {
			parameter.setWeAttributeColor(p);
		}

		p = getParameter("HTMLTagColor");
		if (p != null && p.length() > 0) {
			parameter.setHTMLTagColor(p);
		}

		p = getParameter("HTMLAttributeColor");
		if (p != null && p.length() > 0) {
			parameter.setHTMLAttributeColor(p);
		}

		p = getParameter("piColor");
		if (p != null && p.length() > 0) {
			parameter.setPiColor(p);
		}

		p = getParameter("commentColor");
		if (p != null && p.length() > 0) {
			parameter.setCommentColor(p);
		}

		p = getParameter("normalColor");
		if (p != null && p.length() > 0) {
			parameter.setNormalColor(p);
		}

		p = getParameter("fontName");
		if (p != null && p.length() > 0) {
			parameter.setFontName(p);
		}

		p = getParameter("fontSize");
		if (p != null && p.length() > 0) {
			parameter.setFontSize(Integer.valueOf(p));
		}

		p = getParameter("contentType");
		if (p != null && p.length() > 0) {
			parameter.setContentType(p);
		}

		php_ext = getParameter("phpext");
		if (php_ext == null) {
			php_ext = ".php";
		}
		this.setVisible(false);
		editor = new EditorPanel(this);
		searchAndReplace = new SearchAndReplace(new javax.swing.JFrame(), false, this);
	}


	@Override
	public void start() {
		super.start();
		System.out.println("start");
		System.out.flush();
		if (this.serverUrl != null && this.editorPath != null) {
//			String vers = getParameter("cache_version");
			showUrl(this.serverUrl + this.editorPath + "/initEditor.html?vers=" + System.currentTimeMillis());
			tags = getFromServer(serverUrl + editorPath + "/getAllTags" + php_ext, "tag");
		}
		getContentPane().add(this.editor, BorderLayout.CENTER);
		this.setVisible(true);
		/*new Thread(new Runnable() {
			public void run() {
				while (true) {
					Map<Thread, StackTraceElement[]> m = Thread.getAllStackTraces();
					int i = 0;
					for (Thread t : m.keySet()) {
						System.out.println(++i);
						for (StackTraceElement s : m.get(t)) {
							System.out.println(s.toString());
						}
					}
					try {
						Thread.sleep(1000);
					} catch (InterruptedException ex) {
					}
				}
			}
		}).start();*/
	}

	@Override
	public void stop() {
		super.stop();
		System.out.println("stop");
		System.out.flush();
	}

	@Override
	public void resize(int width, int height) {
		System.out.println("resize1");
		System.out.flush();
	}

	@Override
	public void resize(Dimension d) {
		System.out.println("resize2");
		System.out.flush();

	}

	@Override
	public void destroy() {
		super.destroy();
		System.out.println("destroy");
		System.out.flush();
	}

	private ArrayList<String> getFromServer(String urlString, String nodeName) {
		URL url = null;

		ArrayList<String> out = new ArrayList<String>();

		try {
			url = new URL(urlString);
		} catch (MalformedURLException e1) {
			System.out.println("Error creating URL " + urlString);
		}
		try {
			HttpURLConnection con = (HttpURLConnection) url.openConnection();
			BufferedReader URLinput = new BufferedReader(new InputStreamReader(con.getInputStream()));
			String line;
			String xmlContent = "";
			while ((line = URLinput.readLine()) != null) {
				xmlContent += (line + "\n");
			}

			DocumentBuilder parser = null;
			try {
				parser = DocumentBuilderFactory.newInstance().newDocumentBuilder();
			} catch (ParserConfigurationException e) {
				System.out.println("Error initializing XML parser");
			}
			try {
				Document doc = parser.parse(new InputSource(new StringReader(xmlContent)));
				NodeList nodes = doc.getElementsByTagName(nodeName);
				for (int i = 0; i < nodes.getLength(); i++) {
					Element element = (Element) nodes.item(i);
					out.add(element.getAttribute("name") + "/" + element.getAttribute("needsEndtag"));
				}

			} catch (SAXException e) {
				System.out.println("Error parsing XML");
			}

		} catch (IOException e1) {
			System.out.println("Error connecting to url: " + urlString);
		}

		return out;
	}

	public ArrayList<String> getTags() {
		return tags;
	}

	public ArrayList<String> getAttribsForTag(String tagName) {
		if (!attribs.containsKey(tagName)) {
			String url = serverUrl + editorPath + "/getAttribsForTag" + php_ext + "?tagName=" + tagName;
			ArrayList<String> attr = getFromServer(url, "attribute");
			attribs.put(tagName, attr);
		}
		return attribs.get(tagName);
	}

	public void setCode(String code) {
		editor.setCode(code);
		cmpCode = code;
		isCodeSet = true;
		System.out.println("code set");
		initUndoManager();
	}

	public void initUndoManager() {
		editor.initUndoManager();
	}

	public String getCode() {
		return editor.getCode();
	}

	@Override
	public void setSize(Dimension d) {
		System.out.println("size set2");
		System.out.flush();

	}

	@Override
	public void setSize(int width, int height) {
		super.setSize(width, height);
		validate();
		System.out.println("size set");
		System.out.flush();
	}

	public void sendCtrlS() {

		if (serverUrl.length() > 0) {
			String url = serverUrl + "/webEdition/we_lcmd" + php_ext + "?wecmd0=trigger_save_document";
			showUrl(url);
		}
	}

	public void showUrl(String url) {
		try {
			this.getAppletContext().showDocument(new URL(url), "load");
		} catch (MalformedURLException e1) {
			System.out.println("Error connecting to URL " + url);
		}
	}

	public EditorPanel getEditor() {
		return editor;
	}

	public boolean isHot() {
		return isHot;
	}

	public void setHot(boolean isHot) {
		this.isHot = isHot;
	}

	public void replaceSelection(String txt) {
		editor.pane.replaceSelection(txt);
	}

	public void insertAtStart(String txt) {
		editor.pane.setCaretPosition(0);
		editor.pane.replaceSelection(txt);
	}

	public void insertAtEnd(String txt) {
		editor.pane.setCaretPosition(editor.pane.getDocument().getEndPosition().getOffset() - 1);
		editor.pane.replaceSelection(txt);
	}

	public void searchAndReplaceVisible(boolean show) {
		searchAndReplace.setVisible(show);
	}

	public int search(String search, boolean caseSens, boolean wholeWords) {
		return search(search, caseSens, wholeWords, -1);
	}

	public int search(String search, boolean caseSens, boolean wholeWords, int startAt) {
		if (search == null) {
			return -1;
		}
		int start = startAt;
		if (startAt == -1) {
			if (editor.pane.getSelectedText() != null) {
				start = editor.pane.getSelectionEnd();
			} else {
				start = editor.pane.getCaretPosition();
			}
		}

		String text = editor.pane.getText();
		if (!caseSens) {
			text = text.toLowerCase();
			search = search.toLowerCase();
		}
		int found = -1;
		if (wholeWords) {
			int tmp = start;
			/*
			 * do{ found=text.indexOf(search, start);
			 *
			 * }while(true);
			 */
		} else {
			found = text.indexOf(search, start);
		}
		if (found == -1) {
			found = text.indexOf(search, 0);
		}
		if (found > -1) {
			editor.pane.select(found, search.length() + found);
			return found;
		}
		return -1;
	}

	public void replace(String search, String replace, boolean caseSens, boolean wholeWords) {
		String selText = editor.pane.getSelectedText();
		if ((selText != null && caseSens && selText.equals(search)) || (selText != null && !caseSens && selText.equalsIgnoreCase(search))) {
			editor.pane.replaceSelection(replace);
			setHot(true);
		} else {
			search(search, caseSens, wholeWords);
			if (editor.pane.getSelectedText() != null) {
				editor.pane.replaceSelection(replace);
				setHot(true);
			}
		}
		search(search, caseSens, wholeWords);
	}

	public void replaceAll(String search, String replace, boolean caseSens, boolean wholeWords) {
		int start = 0;
		boolean hot = false;
		while ((start = search(search, caseSens, wholeWords, start)) > -1) {
			hot = true;
			editor.pane.replaceSelection(replace);
			++start;
		}
		if (hot) {
			setHot(true);
		}
	}
}
