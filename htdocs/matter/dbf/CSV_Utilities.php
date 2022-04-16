<?php

class CSV_Utilities
{

    /**
     * Read from a given csv file to array
     * @param  string $filename
     * @return array
     */
    public static function Read_CSV(string $filename): array
    {
        $rows   = array_map('str_getcsv', file($filename));
        $header = array_shift($rows);
        $header = array_map('trim', $header);
        $csv    = array();
        foreach($rows as $row)
        {
            $row   = array_map('trim', $row);
            $csv[] = array_combine($header, $row);
        }
        return $csv;
    }

    /**
     * Generate downloadable csv file with given array
     *
     * @author Sajidur Rahman
     * @param  array  $list      Input array: if associative, then column names will be the csv header line
     * @param  String $filename  Output filename for downloading
     * @param  String $delimiter
     * @return void
     * @link https://stackoverflow.com/questions/16251625/how-to-create-and-download-a-csv-file-from-php-script
     * @link https://stackoverflow.com/questions/20738329/how-to-call-a-php-function-on-the-click-of-a-button
     */
    public static function Downloadable_CSV (array $list, String $filename, String $delimiter = "," )
    {
        header( 'Content-Type: application/csv' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '";' );


        $handle = fopen( 'php://output', 'w');

        // Use keys as header line
        fputcsv($handle, array_keys( $list['0'] ), $delimiter);

        foreach ( $list as $value )
        {
            fputcsv($handle, $value, $delimiter);
        }

        fclose($handle);
    }
}