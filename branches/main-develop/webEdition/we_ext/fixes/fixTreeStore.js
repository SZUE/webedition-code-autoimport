/**
 * webEdition CMS
 *
 * $Rev: 7210 $
 * $Author: lukasimhof $
 * $Date: 2013-12-28 14:19:02 +0100 (Sa, 28 Dez 2013) $
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
 * @package    webEdition_EXT
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

Ext.override(Ext.data.TreeStore, {
	fillNode: function(node, newNodes) {
		var me = this,
			ln = newNodes ? newNodes.length : 0,
			sorters = me.sorters,
			i, sortCollection,
			needsIndexSort = false,
			performLocalSort = ln && me.sortOnLoad && !me.remoteSort && sorters && sorters.items && sorters.items.length,
			node1, node2;

		for (i = 1; i < ln; i++) {
			node1 = newNodes[i];
			node2 = newNodes[i - 1];
			needsIndexSort = node1[node1.persistenceProperty].index != node2[node2.persistenceProperty].index;
			if (needsIndexSort) {
				break;
			}
		}

		if (performLocalSort) {

			if (needsIndexSort) {
				me.sorters.insert(0, me.indexSorter);
			}
			sortCollection = new Ext.util.MixedCollection();
			sortCollection.addAll(newNodes);
			sortCollection.sort(me.sorters.items);
			newNodes = sortCollection.items;

			me.sorters.remove(me.indexSorter);
		} else if (needsIndexSort) {
			Ext.Array.sort(newNodes, me.sortByIndex);
		}

		node.set('loaded', true);
		for (i = 0; i < ln; i++) {
			node.appendChild(newNodes[i], undefined, true);
		}

		return newNodes;
	}
		});