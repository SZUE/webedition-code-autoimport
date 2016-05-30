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

CodeMirror.defineMode("text/weTmpl", function (config, parserConfig) {
	var webeditionOverlay = {
		startState: function () {
			return {
				'insideTag': false,
				'tagName': "",
				'attrName': "",
				'typeName': "",
				'open': false,
				'close': false,
				'attrActive': false,
				'comment': false
			};
		},
		token: function (stream, state) {
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
												if (state.typeName === "") {
													state.typeName = (state.tagName === "object" ? "object" : "document");
												}
												return ((value - 0 == value) ? ("number" + " we" + state.typeName + "ID-" + value) + " WEID" : "string");
											case "type":
												state.typeName = value;
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
									/* falls through */
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
								/* falls through */
							case '>':
								state.insideTag = false;
								if (state.open) {
									if (state.tagName === "comment") {
										state.comment = true;
									}
									return "weOpenTag weTag weTag_" + state.tagName;
								}
								if (state.tagName === "comment") {
									state.comment = false;
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
				if (state.comment) {
					if (stream.match(/.*<\/we:comment/, true)) {
						stream.backUp(12);
						state.comment = false;
					} else {
						stream.skipToEnd();
					}
					return 'comment';
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
	return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || "application/x-httpd-php"), webeditionOverlay);
});
