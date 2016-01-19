More than 100k products to export?
----------------------------------

We had use cases where customers would export 270k products and had issues with the memory usage.

Most of the massive operations of the PIM like imports and exports are bulked by page of lines (or objects) to avoid too large memory usage.

As each product can have different properties, export kept the transformed array in memory to add missing columns from a line to another.

In the version 1.4.9 (PIM-5127), we changed the internal behavior of the CsvProductWriter to use a file buffer to temporarily write each previously transformed array in order to aggregate the final result.

As a conclusion, for product export, limitation is now the hard drive space and no longer the available memory.

Please notice that the number of values per product will have an impact on the execution time and memory usage.

In the upcoming 1.5 version, this CsvProductWriter is reworked to extract the Buffer component and allow to use this component in other contexts.
