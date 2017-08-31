<?php
class inputClassModel extends Model
{
	public function initModel()
	{
		$this->settable('flow_element');
	}
	
	
	public function getsubtable($modeid,$iszb=1, $hang=1, $ism=0)
	{
		if($iszb<=0)$iszb=1;
		if($hang<=0)$hang=1;
		
		$rows 	= $this->getall("`mid`='$modeid' and `iszb`=$iszb and `islu`=1",'`isbt`,`fields`,`fieldstype`,`name`','`sort`');
		if(!$rows)return '';
		$xu	 = $iszb-1;
		$str = '<table class="tablesub" id="tablesub'.$xu.'" style="width:100%;" border="0" cellspacing="0" cellpadding="0">';
		$str.='<tr>';
		$str.='<td width="10%" nowrap>序号</td>';
		$yfsr= array('hidden','fixed');
		$zlen= 0;
		foreach($rows as $k=>$rs){
			$zlen++;
			$xh = '';
			if($rs['isbt']==1)$xh='*';
			if(in_array($rs['fieldstype'], $yfsr))continue;
			$str.='<td nowrap>'.$xh.''.$rs['name'].'</td>';
		}
		$str.='<td width="5%" nowrap>操作</td>';
		$str.='</tr>';
		for($j=0;$j<$hang;$j++){
			$str.='<tr>';
			$str.='<td>[xuhao'.$xu.','.$j.']</td>';
			foreach($rows as $k=>$rs){
				if(in_array($rs['fieldstype'], $yfsr))continue;
				$str.='<td>['.$rs['fields'].''.$xu.','.$j.']';
				if(isset($rows[$k+1]) && in_array($rows[$k+1]['fieldstype'], $yfsr))$str.='['.$rows[$k+1]['fields'].''.$xu.','.$j.']';
				$str.='</td>';
			}
			$str.='<td>{删,'.$xu.'}</td>';
			$str.='</tr>';
		}
		$str.='</table>';
		if($ism==0)$str.='<div style="background-color:#F1F1F1;">{新增,'.$xu.'}</div>';
		if($ism==1)$str.='<div>{新增,'.$xu.'}</div>';
		return $str;
	}
}