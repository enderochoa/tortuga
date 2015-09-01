
  <div>

    <h2>DataSet</h2>


    <table>
    <?php foreach ($items as $item):?>
      <tr>
       <td><?php echo $item['title']?></td><td><?php echo $item['body']?></td>
      </tr>
    <?php endforeach;?>
    </table>
    <br />
    <?php echo $navigator;?>
       
  </div>
