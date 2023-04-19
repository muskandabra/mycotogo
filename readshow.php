

<?php

$CSVfp = fopen('template-data_2023-04-11.csv','r');
if ($CSVfp !== FALSE) {
    ?>
    <div >
        <table >
            <!--thead>
                <tr>
                    <th>NAME</th>
                    <th>COLOR</th>
                </tr>
            </thead-->
<?php
    while (! feof($CSVfp)) {
        $data = fgetcsv($CSVfp, 1000, ",");
        if (! empty($data)) {
            ?>
            <tr >
              
                <td><?php echo @$data[1]; ?></td>
                <td><?php echo @$data[2]; ?></td>
                <td><?php echo @$data[4]; ?></td>
            
            </tr>
 <?php }?>
<?php
    }
    ?>
        </table>
    </div>
<?php
}
fclose($CSVfp);
?>


