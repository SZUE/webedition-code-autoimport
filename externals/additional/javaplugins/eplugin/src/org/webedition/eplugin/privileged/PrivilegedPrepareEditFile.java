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
package org.webedition.eplugin.privileged;

import java.io.File;
import java.net.URL;
import java.security.PrivilegedAction;
import java.util.HashMap;
import org.webedition.eplugin.util.CopyUtility;

public class PrivilegedPrepareEditFile implements PrivilegedAction<String> {

	private final URL SourceUrl;
	private final String DestinationFilename;
	private final HashMap<String, String> request;

	public PrivilegedPrepareEditFile(URL url, String filename, HashMap<String, String> request) {
		SourceUrl = url;
		DestinationFilename = filename;
		this.request = request;
	}

	@Override
	public String run() {
		try {
			File d = new File(DestinationFilename).getParentFile();
			if (!d.exists()) {
				d.mkdirs();
			}

			CopyUtility.copy(SourceUrl, DestinationFilename, request);

			return DestinationFilename;

		} catch (java.io.IOException e) {
		}

		return "";

	}
}
