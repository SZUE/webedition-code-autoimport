/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


CodeMirror.defineMode("text/weTmpl", function(config, parserConfig) {
	var webeditionOverlay = {
		startState: function() {
			return {
				insideTag: false,
				tagName: "",
				attrName: "",
				typeName: "",
				open: false,
				close: false,
				attrActive: false
			};
		},
		token: function(stream, state) {
			if (state.insideTag) {
				if (state.close) {
					if (stream.skipTo(">")) {
						stream.next();
					}
					state.insideTag = false;
					state.typeName = "";
					return "weCloseTag weTag";
				}
				if (state.open) {
					if (stream.eatSpace()) {
						return null;
					}
					if (state.attrActive) {
						stream.next();//consume =
						quot = false;
						var value = "";
						while ((ch = stream.next()) !== null && ch !== undefined) {
							switch (ch) {
								case "\\":
									if (stream.peek() === "\"") {
										stream.next();
									}
									continue;
								case "\"":
									if (quot) {
										state.attrActive = false;
										switch (state.attrName) {
											case "id":
												if (state.typeName == "") {
													state.typeName = (state.tagName === "object" ? "object" : "document");
												}
												return ((value - 0 == value) ? ("number" + " we" + state.typeName + "ID-" + value)+" WEID" : "string");
											case "type":
												console.log(value);
												state.typeName = value;

												console.log(state.typeName);
												return null;
										}
										return null;
									}
									quot = true;
									continue;
								default:
									value += ch;
							}
						}
					} else {
						var attrName = "";
						state.attrActive = true;
						while ((ch = stream.next()) !== null && ch !== undefined) {
							switch (ch) {
								default:
									attrName += ch;
									stream.eatSpace();
									if (stream.peek() === "=") {
										state.attrName = attrName;
										return "weTagAttribute weTag_" + state.tagName + "_" + attrName;
									}
									continue;
								case "/":
									if (stream.skipTo('>')) {
										stream.next();
									}
								case ">":
									state.insideTag = false;
									return "weTag " + attrName;
							}
						}
					}
				}
				stream.skipToEnd();
				return null;
			} else {
				state.open = stream.match("<we:");
				state.close = !state.open && stream.match("</we:");
				state.attrActive = false;
				if (state.open || state.close) {
					state.insideTag = true;
					state.tagName = "";
					state.typeName = "";
					state.attrName = "";

					while ((ch = stream.next()) !== null && ch !== undefined) {
						switch (ch) {
							default:
								state.tagName += ch;
								continue;
							case '/':
								if (state.open && (stream.eatSpace() | stream.peek() === ">")) {
									stream.next();
									state.insideTag = false;
									return "weSelfClose weTag weTag_" + state.tagName;
								}
							case '>':
								state.insideTag = false;
								if (state.open) {
									return "weOpenTag weTag weTag_" + state.tagName;
								}
								return "weCloseTag weTag";

							case ' ':
								stream.eatSpace();
								if (stream.peek() === ">" || stream.peek() === "/") {
									continue;
								}
								return "weOpenTag weTag weTag_" + state.tagName;
						}
						if (ch === " " || ch === "/" || ch === ">") {
							active = false;
						} else {
							name += ch;
						}
					}
				}
				if (!stream.eol() && stream.peek() === "<") {
					stream.next();
				}
				if (!stream.skipTo("<")) {
					stream.skipToEnd();
				}
				return null;
			}
		}
	};
	return CodeMirror.overlayParser(CodeMirror.getMode(config, parserConfig.backdrop || "application/x-httpd-php"), webeditionOverlay);
});