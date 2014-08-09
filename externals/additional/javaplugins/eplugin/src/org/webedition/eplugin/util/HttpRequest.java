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
package org.webedition.eplugin.util;

import java.io.File;
import java.io.FileInputStream;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.URL;
import java.net.URLConnection;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Map.Entry;

public class HttpRequest {

	protected String FilePath;
	protected HashMap<String, String> RequestProperties;
	protected HashMap<String, String> Variables;
	protected HashMap<String, String> Files;
	protected String ContentBoundary = "----------NFD3nEd4J9z0Wedx9oM36r";

	public HttpRequest(HashMap<String, String> RequestProperties) {

		Variables = new HashMap<String, String>();
		Files = new HashMap<String, String>();

		this.RequestProperties = RequestProperties;
		this.RequestProperties.put("Content-Type", "multipart/form-data; boundary=" + ContentBoundary);

	}

	public void addVariable(String name, String content) {
		Variables.put(name, content);
	}

	public void addFile(String name, String path) {
		Files.put(name, path);
	}

	public String upload(String UploadURL) {

		FileInputStream fileInputStream = null;
		OutputStream output = null;
		InputStream input = null;
		String Reponse = "";

		try {

			URL postURL = new URL(UploadURL);
			URLConnection connection = postURL.openConnection();

			connection.setDoOutput(true); // turns it into a post

			String name;

			/*
			 * if(Files.size()>0) {
			 * RequestProperties.put("Content-Type","multipart/form-data; boundary=" +
			 * ContentBoundary);
			 }
			 */
			for (Entry<String, String> ent : RequestProperties.entrySet()) {
				connection.setRequestProperty(ent.getKey(), ent.getValue());
			}
			connection.setUseCaches(false);

			output = connection.getOutputStream();

			String startStr = "--" + ContentBoundary + "\r\n";
			String endStr = "\r\n--" + ContentBoundary + "--\r\n";
			for (Entry<String, String> ent : Variables.entrySet()) {
				name = ent.getKey();
				output.write((startStr + "Content-Disposition: form-data; name=\"" + name + "\";\r\nContent-Type: text/plain\r\n\r\n").getBytes());
				output.write(((String) ent.getValue()).getBytes());
				output.write(endStr.getBytes());
			}

			int readByte = -1;
			for (Entry<String, String> ent : Files.entrySet()) {
				name = ent.getKey();

				output.write((startStr + "Content-Disposition: form-data; name=\"" + name + "\"; filename=\"" + ent.getValue() + "\"\r\nContent-Type: application/octet-stream\r\n\r\n").getBytes());

				fileInputStream = new FileInputStream(new File(ent.getValue()));

				byte[] fileBuffer = new byte[512];
				int totalByte = 0;
				while ((readByte = fileInputStream.read(fileBuffer, 0, 512)) != -1) {
					totalByte += readByte;
					output.write(fileBuffer, 0, readByte);
				}
				System.out.println("wrote " + totalByte);
				fileInputStream.close();
				fileInputStream = null;
				output.write((endStr).getBytes());
				output.flush();
			}

			input = connection.getInputStream();

			/*
			 * byte[] buffer = new byte[512]; readByte = input.read(buffer, 0, 512);
			 *
			 * while (readByte != -1) { Reponse += new String(buffer,0,readByte);
			 * readByte = input.read(buffer, 0, 512); }
			 */
			StreamWrapper reader = new StreamWrapper(input);
			reader.start();
			reader.join();
			Reponse = reader.getResult();

			//System.out.println(Reponse);

			input.close();
			input = null;
			output.close();
			output = null;
			reader = null;

		} catch (Exception ex) {
			System.err.println("Exception: " + ex);
			ex.printStackTrace();
		} finally {
			if (fileInputStream != null) {
				try {
					fileInputStream.close();
				} catch (Exception ex) {
					System.err.println("Exception: " + ex);
					ex.printStackTrace();
				}
			}
			if (input != null) {
				try {
					input.close();
				} catch (Exception ex) {
					System.err.println("Exception: " + ex);
					ex.printStackTrace();
				}
			}
			if (output != null) {
				try {
					output.close();
				} catch (Exception ex) {
					System.err.println("Exception: " + ex);
					ex.printStackTrace();
				}
			}
		}
		return Reponse;

	}
}
