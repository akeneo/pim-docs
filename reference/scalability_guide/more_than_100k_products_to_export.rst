More than 100k products to export?
----------------------------------

In the past, we encountered use cases where partners would export 270k products and experienced issues with the memory usage.

Most of PIM's massive operations like imports and exports are #bulked by page of lines (or objects)# to avoid too large memory usage. #bulked by page of lines ?""

As each product may have different properties, the export operation would keep the transformed array in memory in order to add missing columns from one line to another.

In version 1.4.9 (PIM-5127), we changed the internal behavior of the CsvProductWriter to use a file buffer to temporarily write each previously transformed array in order to aggregate the final result.

As a conclusion, the main product export's limitation is now the hard drive space and no longer the available memory.

Please notice that the number of values per product will have an impact on the execution time and memory usage.

In the upcoming 1.5 version, the CsvProductWriter is reworked to extract the buffer component and allow its use in other contexts.#TAG pas saisis extract buffer component = file ?
