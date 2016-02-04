<?php

// SeniorID
// PensionerID
// InclusionDate
// hh_id
// osca_ID
// PlaceIssued
// DateIssued
// firstname
// middlename
// lastname
// extname
// Birthdate
// sex
// MaritalID
// affliationID
// psgc_region
// psgc_province
// psgc_municipality
// psgc_brgy
// given_add
// Status
// paymentmodeID
// approved
// approvedby
// DateApproved
// ArrangementID
// is_4ps
// abandoned
// Createdby
// CreatedDate
// UpdatedBy
// UpdatedDate
// UpdateRemarks
// codeGen

?>
<?php if ($tbl_pensioner->Visible) { ?>
<table cellspacing="0" id="t_tbl_pensioner" class="ewGrid"><tr><td>
<table id="tbl_tbl_pensionermaster" class="table table-bordered table-striped">
	<tbody>
<?php if ($tbl_pensioner->SeniorID->Visible) { // SeniorID ?>
		<tr id="r_SeniorID">
			<td><?php echo $tbl_pensioner->SeniorID->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->SeniorID->CellAttributes() ?>>
<span id="el_tbl_pensioner_SeniorID" class="control-group">
<span<?php echo $tbl_pensioner->SeniorID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->SeniorID->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->PensionerID->Visible) { // PensionerID ?>
		<tr id="r_PensionerID">
			<td><?php echo $tbl_pensioner->PensionerID->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->PensionerID->CellAttributes() ?>>
<span id="el_tbl_pensioner_PensionerID" class="control-group">
<span<?php echo $tbl_pensioner->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->PensionerID->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->InclusionDate->Visible) { // InclusionDate ?>
		<tr id="r_InclusionDate">
			<td><?php echo $tbl_pensioner->InclusionDate->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->InclusionDate->CellAttributes() ?>>
<span id="el_tbl_pensioner_InclusionDate" class="control-group">
<span<?php echo $tbl_pensioner->InclusionDate->ViewAttributes() ?>>
<?php echo $tbl_pensioner->InclusionDate->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->hh_id->Visible) { // hh_id ?>
		<tr id="r_hh_id">
			<td><?php echo $tbl_pensioner->hh_id->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->hh_id->CellAttributes() ?>>
<span id="el_tbl_pensioner_hh_id" class="control-group">
<span<?php echo $tbl_pensioner->hh_id->ViewAttributes() ?>>
<?php echo $tbl_pensioner->hh_id->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->osca_ID->Visible) { // osca_ID ?>
		<tr id="r_osca_ID">
			<td><?php echo $tbl_pensioner->osca_ID->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->osca_ID->CellAttributes() ?>>
<span id="el_tbl_pensioner_osca_ID" class="control-group">
<span<?php echo $tbl_pensioner->osca_ID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->osca_ID->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->PlaceIssued->Visible) { // PlaceIssued ?>
		<tr id="r_PlaceIssued">
			<td><?php echo $tbl_pensioner->PlaceIssued->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->PlaceIssued->CellAttributes() ?>>
<span id="el_tbl_pensioner_PlaceIssued" class="control-group">
<span<?php echo $tbl_pensioner->PlaceIssued->ViewAttributes() ?>>
<?php echo $tbl_pensioner->PlaceIssued->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->DateIssued->Visible) { // DateIssued ?>
		<tr id="r_DateIssued">
			<td><?php echo $tbl_pensioner->DateIssued->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->DateIssued->CellAttributes() ?>>
<span id="el_tbl_pensioner_DateIssued" class="control-group">
<span<?php echo $tbl_pensioner->DateIssued->ViewAttributes() ?>>
<?php echo $tbl_pensioner->DateIssued->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->firstname->Visible) { // firstname ?>
		<tr id="r_firstname">
			<td><?php echo $tbl_pensioner->firstname->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->firstname->CellAttributes() ?>>
<span id="el_tbl_pensioner_firstname" class="control-group">
<span<?php echo $tbl_pensioner->firstname->ViewAttributes() ?>>
<?php echo $tbl_pensioner->firstname->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->middlename->Visible) { // middlename ?>
		<tr id="r_middlename">
			<td><?php echo $tbl_pensioner->middlename->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->middlename->CellAttributes() ?>>
<span id="el_tbl_pensioner_middlename" class="control-group">
<span<?php echo $tbl_pensioner->middlename->ViewAttributes() ?>>
<?php echo $tbl_pensioner->middlename->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->lastname->Visible) { // lastname ?>
		<tr id="r_lastname">
			<td><?php echo $tbl_pensioner->lastname->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->lastname->CellAttributes() ?>>
<span id="el_tbl_pensioner_lastname" class="control-group">
<span<?php echo $tbl_pensioner->lastname->ViewAttributes() ?>>
<?php echo $tbl_pensioner->lastname->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->extname->Visible) { // extname ?>
		<tr id="r_extname">
			<td><?php echo $tbl_pensioner->extname->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->extname->CellAttributes() ?>>
<span id="el_tbl_pensioner_extname" class="control-group">
<span<?php echo $tbl_pensioner->extname->ViewAttributes() ?>>
<?php echo $tbl_pensioner->extname->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->Birthdate->Visible) { // Birthdate ?>
		<tr id="r_Birthdate">
			<td><?php echo $tbl_pensioner->Birthdate->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->Birthdate->CellAttributes() ?>>
<span id="el_tbl_pensioner_Birthdate" class="control-group">
<span<?php echo $tbl_pensioner->Birthdate->ViewAttributes() ?>>
<?php echo $tbl_pensioner->Birthdate->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->sex->Visible) { // sex ?>
		<tr id="r_sex">
			<td><?php echo $tbl_pensioner->sex->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->sex->CellAttributes() ?>>
<span id="el_tbl_pensioner_sex" class="control-group">
<span<?php echo $tbl_pensioner->sex->ViewAttributes() ?>>
<?php echo $tbl_pensioner->sex->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->MaritalID->Visible) { // MaritalID ?>
		<tr id="r_MaritalID">
			<td><?php echo $tbl_pensioner->MaritalID->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->MaritalID->CellAttributes() ?>>
<span id="el_tbl_pensioner_MaritalID" class="control-group">
<span<?php echo $tbl_pensioner->MaritalID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->MaritalID->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->affliationID->Visible) { // affliationID ?>
		<tr id="r_affliationID">
			<td><?php echo $tbl_pensioner->affliationID->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->affliationID->CellAttributes() ?>>
<span id="el_tbl_pensioner_affliationID" class="control-group">
<span<?php echo $tbl_pensioner->affliationID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->affliationID->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->psgc_region->Visible) { // psgc_region ?>
		<tr id="r_psgc_region">
			<td><?php echo $tbl_pensioner->psgc_region->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->psgc_region->CellAttributes() ?>>
<span id="el_tbl_pensioner_psgc_region" class="control-group">
<span<?php echo $tbl_pensioner->psgc_region->ViewAttributes() ?>>
<?php echo $tbl_pensioner->psgc_region->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->psgc_province->Visible) { // psgc_province ?>
		<tr id="r_psgc_province">
			<td><?php echo $tbl_pensioner->psgc_province->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->psgc_province->CellAttributes() ?>>
<span id="el_tbl_pensioner_psgc_province" class="control-group">
<span<?php echo $tbl_pensioner->psgc_province->ViewAttributes() ?>>
<?php echo $tbl_pensioner->psgc_province->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->psgc_municipality->Visible) { // psgc_municipality ?>
		<tr id="r_psgc_municipality">
			<td><?php echo $tbl_pensioner->psgc_municipality->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->psgc_municipality->CellAttributes() ?>>
<span id="el_tbl_pensioner_psgc_municipality" class="control-group">
<span<?php echo $tbl_pensioner->psgc_municipality->ViewAttributes() ?>>
<?php echo $tbl_pensioner->psgc_municipality->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->psgc_brgy->Visible) { // psgc_brgy ?>
		<tr id="r_psgc_brgy">
			<td><?php echo $tbl_pensioner->psgc_brgy->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->psgc_brgy->CellAttributes() ?>>
<span id="el_tbl_pensioner_psgc_brgy" class="control-group">
<span<?php echo $tbl_pensioner->psgc_brgy->ViewAttributes() ?>>
<?php echo $tbl_pensioner->psgc_brgy->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->given_add->Visible) { // given_add ?>
		<tr id="r_given_add">
			<td><?php echo $tbl_pensioner->given_add->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->given_add->CellAttributes() ?>>
<span id="el_tbl_pensioner_given_add" class="control-group">
<span<?php echo $tbl_pensioner->given_add->ViewAttributes() ?>>
<?php echo $tbl_pensioner->given_add->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->Status->Visible) { // Status ?>
		<tr id="r_Status">
			<td><?php echo $tbl_pensioner->Status->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->Status->CellAttributes() ?>>
<span id="el_tbl_pensioner_Status" class="control-group">
<span<?php echo $tbl_pensioner->Status->ViewAttributes() ?>>
<?php echo $tbl_pensioner->Status->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->paymentmodeID->Visible) { // paymentmodeID ?>
		<tr id="r_paymentmodeID">
			<td><?php echo $tbl_pensioner->paymentmodeID->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->paymentmodeID->CellAttributes() ?>>
<span id="el_tbl_pensioner_paymentmodeID" class="control-group">
<span<?php echo $tbl_pensioner->paymentmodeID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->paymentmodeID->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->approved->Visible) { // approved ?>
		<tr id="r_approved">
			<td><?php echo $tbl_pensioner->approved->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->approved->CellAttributes() ?>>
<span id="el_tbl_pensioner_approved" class="control-group">
<span<?php echo $tbl_pensioner->approved->ViewAttributes() ?>>
<?php echo $tbl_pensioner->approved->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->approvedby->Visible) { // approvedby ?>
		<tr id="r_approvedby">
			<td><?php echo $tbl_pensioner->approvedby->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->approvedby->CellAttributes() ?>>
<span id="el_tbl_pensioner_approvedby" class="control-group">
<span<?php echo $tbl_pensioner->approvedby->ViewAttributes() ?>>
<?php echo $tbl_pensioner->approvedby->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->DateApproved->Visible) { // DateApproved ?>
		<tr id="r_DateApproved">
			<td><?php echo $tbl_pensioner->DateApproved->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->DateApproved->CellAttributes() ?>>
<span id="el_tbl_pensioner_DateApproved" class="control-group">
<span<?php echo $tbl_pensioner->DateApproved->ViewAttributes() ?>>
<?php echo $tbl_pensioner->DateApproved->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->ArrangementID->Visible) { // ArrangementID ?>
		<tr id="r_ArrangementID">
			<td><?php echo $tbl_pensioner->ArrangementID->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->ArrangementID->CellAttributes() ?>>
<span id="el_tbl_pensioner_ArrangementID" class="control-group">
<span<?php echo $tbl_pensioner->ArrangementID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->ArrangementID->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->is_4ps->Visible) { // is_4ps ?>
		<tr id="r_is_4ps">
			<td><?php echo $tbl_pensioner->is_4ps->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->is_4ps->CellAttributes() ?>>
<span id="el_tbl_pensioner_is_4ps" class="control-group">
<span<?php echo $tbl_pensioner->is_4ps->ViewAttributes() ?>>
<?php echo $tbl_pensioner->is_4ps->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->abandoned->Visible) { // abandoned ?>
		<tr id="r_abandoned">
			<td><?php echo $tbl_pensioner->abandoned->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->abandoned->CellAttributes() ?>>
<span id="el_tbl_pensioner_abandoned" class="control-group">
<span<?php echo $tbl_pensioner->abandoned->ViewAttributes() ?>>
<?php echo $tbl_pensioner->abandoned->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->Createdby->Visible) { // Createdby ?>
		<tr id="r_Createdby">
			<td><?php echo $tbl_pensioner->Createdby->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->Createdby->CellAttributes() ?>>
<span id="el_tbl_pensioner_Createdby" class="control-group">
<span<?php echo $tbl_pensioner->Createdby->ViewAttributes() ?>>
<?php echo $tbl_pensioner->Createdby->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->CreatedDate->Visible) { // CreatedDate ?>
		<tr id="r_CreatedDate">
			<td><?php echo $tbl_pensioner->CreatedDate->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->CreatedDate->CellAttributes() ?>>
<span id="el_tbl_pensioner_CreatedDate" class="control-group">
<span<?php echo $tbl_pensioner->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_pensioner->CreatedDate->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->UpdatedBy->Visible) { // UpdatedBy ?>
		<tr id="r_UpdatedBy">
			<td><?php echo $tbl_pensioner->UpdatedBy->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->UpdatedBy->CellAttributes() ?>>
<span id="el_tbl_pensioner_UpdatedBy" class="control-group">
<span<?php echo $tbl_pensioner->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_pensioner->UpdatedBy->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->UpdatedDate->Visible) { // UpdatedDate ?>
		<tr id="r_UpdatedDate">
			<td><?php echo $tbl_pensioner->UpdatedDate->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->UpdatedDate->CellAttributes() ?>>
<span id="el_tbl_pensioner_UpdatedDate" class="control-group">
<span<?php echo $tbl_pensioner->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_pensioner->UpdatedDate->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->UpdateRemarks->Visible) { // UpdateRemarks ?>
		<tr id="r_UpdateRemarks">
			<td><?php echo $tbl_pensioner->UpdateRemarks->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->UpdateRemarks->CellAttributes() ?>>
<span id="el_tbl_pensioner_UpdateRemarks" class="control-group">
<span<?php echo $tbl_pensioner->UpdateRemarks->ViewAttributes() ?>>
<?php echo $tbl_pensioner->UpdateRemarks->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($tbl_pensioner->codeGen->Visible) { // codeGen ?>
		<tr id="r_codeGen">
			<td><?php echo $tbl_pensioner->codeGen->FldCaption() ?></td>
			<td<?php echo $tbl_pensioner->codeGen->CellAttributes() ?>>
<span id="el_tbl_pensioner_codeGen" class="control-group">
<span<?php echo $tbl_pensioner->codeGen->ViewAttributes() ?>>
<?php echo $tbl_pensioner->codeGen->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
	</tbody>
</table>
</td></tr></table>
<?php } ?>
