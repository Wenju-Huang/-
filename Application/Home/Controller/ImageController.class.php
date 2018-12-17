<?php
namespace Home\Controller;
use Think\Controller;
class ImageController extends Controller{
	
	public $roomModel;
	
	public function _initialize()
	{
		$this->roomModel = M("room");
	}
	public function index(){	
		$resData = array(
			"code"=>"000",
			"message"=>'unoccupied!',
		);
		
		$json_string = file_get_contents("php://input");
		if(IS_POST && !empty($json_string)){	
			$obj=json_decode($json_string);
			$roomNum = $obj->roomNum;
			if(!empty($roomNum))
			{
				$roomDate = $this->roomModel->where("roomNum='{$roomNum}'")->find();
				if($roomDate["takephoto"]){
					$resData["code"] = "3002";
					$resData["message"] = "take photo!";
					$this->roomModel->takephoto = 0;
					$this->roomModel->where("roomNum='{$roomNum}'")->save();
				}
			}
			else{
				$resData["code"] = "4002";
				$resData["message"] = "empty roomNum!";
			}
		}
		
		echo json_encode($resData);
	}
	public function takePhoto(){
		$imageUrl = $this->roomModel->where("roomNum='{$roomNum}'")->getField('image');
		$this->roomModel->takephoto = 1;
		$this->roomModel->where("roomNum='6-420'")->save();
		while($imageUrl == $this->roomModel->where("roomNum='{$roomNum}'")->getField('image'));
	}
	public function uploads($imageName){		
		if(IS_POST)
		{		
			$upload = new \Think\Upload();//  ʵ�����ϴ���
			$upload->maxSize = 3145728 ;//  ���ø����ϴ���С
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');//  ���ø����ϴ�����
			$upload->savePath = './image/'; //  ���ø����ϴ�Ŀ¼
			$upload->saveName = "time";
			//  �ϴ������ļ�
			$info = $upload->uploadOne($_FILES[$imageName]);
			if(!$info) {//  �ϴ�������ʾ������Ϣ
				//$this->error($upload->getError());
				echo $upload->getError();
			}else{//  �ϴ��ɹ� ��ȡ�ϴ��ļ���Ϣ
				$this->roomModel->photo = $info['savepath'].$info['savename'];
				$this->roomModel->takephoto = 0;
				$this->roomModel->newphoto = 1;
				$this->roomModel->where("roomNum='6-420'")->save();
				
				/********��ͼƬ��ַ 3/12�Ÿ�**********/
				$photoMaode = M("photo");
				$photo["photo_url"] = "".C("ImagePath").$info['savepath'].$info['savename'];
				$photo["date"] = date("Y-m-d",time());
				$photo["time"] = date("H:i:s",time());
				$photoMaode->data($photo)->add();
				
				echo $info['savepath'].$info['savename'];
			}
		}
	}
}