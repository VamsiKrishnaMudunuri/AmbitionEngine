@extends('layouts.page')
@section('title', Translator::transSmart('app.Terms and Conditions', 'Terms and Conditions'))

@section('styles')
    @parent
    <style>
        .table-list {
            margin-bottom: 0px;
        }

        .table-borderless > tbody > tr > td,
        .table-borderless > tbody > tr > th,
        .table-borderless > tfoot > tr > td,
        .table-borderless > tfoot > tr > th,
        .table-borderless > thead > tr > td,
        .table-borderless > thead > tr > th {
            border: none;
        }
    </style>
@endsection

@section('scripts')
    @parent

@endsection



@section('container', 'container')

@section('content')

    <div class="page-term">
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h2>
                    <b>
                        Terms & Conditions
                    </b>
                </h2>
            </div>
        </div>
        <br />

        <div class="row">
            <div class="col-xs-12 col-sm-12">
               <p>
                   By accessing or using COMMON GROUND’s ("<b>Service Provider</b>") services in any way, you agree to be bound by these Terms and Conditions ("<b>T&C</b>"), our Rules and Regulations and our Privacy Policy which is available at <a href="{{URL::route('page::index')}}">{{URL::route('page::index')}}</a>.
               </p>
               <br />
               <p>
                   a. You acknowledge and agree that the Service Provider reserve the right, from time to time, to make modification, deletions or additions to this T&C.
               </p>
               <p>
                    b. By selecting the Service Provider’s Membership Plans (as herein defined), you agree and consent that this T&C constitutes an agreement between you and the Service Provider.
               </p>
               <p>
                    c. All terms and references used in this T&C and which are defined and construed in your Membership Plans respectively but are not defined or construed in this T&C shall have the same meaning and construction as per your Membership Plans.
               </p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        1. IDENTIFICATION & AUTHORIZATION
                    </b>
                </h3>
                <br />
                <p>
                    You warrant and represent that your Personal Details and Contact Details in relation to your identity is accurate, precise and complete; and agree, if upon any request at any time by the Service Provider, to present a valid, government-issued photo identification for verification purposes. You agree that, in any event you are the authorized representative of an individual, agent, sole proprietor, company, or entity; you have obtained the lawful authority via written authorization or consent from such individual, agent, sole proprietor, company or entity. You agree not to impersonate or represent intentionally or unintentionally, in any way whatsoever, any third party, individual, agent, sole proprietor, company or entity without lawful authority; or otherwise provide, submit or present any false and/or misleading information to the Service Provider.
                </p>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        2. SERVICES
                    </b>
                </h3>
                <br />
                <p>
                    Following your Membership Plan, you will be accorded enjoyment of services subject to these T&C. You agree that references to "Services" means you access and use of space ("<b>Space</b>") at the Center. Your choices of Membership Plans are as follows:
                </p>
                <br />

                <p>
                    Lite Membership Plan; or
                </p>

                <p>
                    Hot Desk Plan - a flexible choice of any available working desk space in an open area shared with other members at the agreed office location ; or
                </p>
                <p>
                    Fixed Desk Plan - Dedicated Desk membership plan: an assigned desk space in an open area shared with other members at the agreed office location ; or
                </p>
                <p>
                    Private Office Plan - Dedicated Office Space membership plan: an assigned closed off office space in a closed area shared with only the members of your company at the agreed office location; or
                </p>
                <p>
                    Enterprise Office Plan - Dedicated Large Format Office Space membership plan: an assigned closed off office space in a closed area shared with only the members of your company at the agreed office location ; or
                </p>
                <p>
                    Meeting Rooms and Event Space Plan; or
                </p>
                <p>
                    Day Passes Plan.
                </p>
                <p>
                    Following your choice of plans, you will be provided Membership Benefits of which the Service Provider may at its absolute discretion consider necessary to provide to you.
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        3. USAGE OF SERVICES & ADDITIONAL SERVICES
                    </b>
                </h3>
                <br />
                <h4>
                    <b>
                       3.1 Compliance
                    </b>
                </h4>
                <br />
                <p>
                    You agree to observe, adhere and comply strictly with this T&C, including all warranties, representations, instructions, whether express or implied and the terms of Privacy Notice and Rules of Regulations of the Service Provider.
                </p>

                <p>
                    You agree to observe, adhere and comply strictly with house rules governing your expected behavior at the Center ("<b>House Rules</b>"). The House Rules is available for inspection at the Center and you acknowledge and agree that the House Rules may be revised from time to time. In general, you agree to not perform or cause to perform, do or cause to do any act that is or potentially will be disruptive, damaging or dangerous to the Service Provider, other members of the Service Provider, agents, guests, or any parties of the foregoing.
                </p>

                <p>
                    You agree that there are some Services which may be subjected to additional guidelines, terms, conditions and/or rules, which will otherwise be communicated via any reasonable means by the Service Provider to you.
                </p>
                <br />
                <h4>
                    <b>
                        3.2 Availability
                    </b>
                </h4>
                <br />
                <p>
                    a. The Service Provider will use commercially reasonable efforts to provide you the Services and the Membership Benefits to you from time to time during the Term.
                </p>
                <p>
                    b. You understand that if you are unable to peruse the Services due to any reasons whatsoever including

                </p>
                <table class="table table-condensed table-borderless table-list">
                    <colgroup>
                        <col width="2%">
                        <col width="2%">
                        <col width="96%">
                    </colgroup>
                    <tr>
                        <td></td>
                        <td>
                            i)
                        </td>
                        <td>
                            construction and/or renovation plans,
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            ii)
                        </td>
                        <td>
                            difficulty in procuring any permits or permission necessary, or
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            iii)
                        </td>
                        <td>
                            any disruptions, delays, emergency and/or any unforeseen events, you cannot challenge the validity of this T&C and/or make any request, claim or demand against the Service Provider for compensation or any other demand.
                        </td>
                    </tr>
                </table>

                <br />
                <h4>
                    <b>
                        3.3 Modification
                    </b>
                </h4>
                <br />
                <p>
                    a. You acknowledge and agree that the Service Provider reserves its sole and absolute discretion to change, modify, amend, delete, add, and/or update any and all terms of Services, the Membership Plans, and the Membership Benefits including fee rates, payment method, payment obligations at any time, from time to time; and such changes, modifications, amendments, deletion, addition and/or updates will be duly communicated to you via any reasonable means at the discretion of the Service Provider and such changes, modifications, amendments, deletion, addition and/or updates shall take effect immediately upon such notice.
                </p>
                <br />
                <h4>
                    <b>
                        3.4 Accessibility
                    </b>
                </h4>
                <br />
                <p>
                    a. The Services are accessible during hours as stated in your Membership Plan.
                </p>
                <p>
                    b. You acknowledge and agree that the Service Provider reserves the right to regularly record video of areas of the Center, at all time including Regular Business Hours for security purposes.
                </p>

            </div>
        </div>


        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        4. DEPOSIT
                    </b>
                </h3>
                <br />
                <p>
                    a. You may be required to pay a refundable Deposit of amount as stated in your Membership Plan ("<b>Membership Deposit</b>").
                </p>

                <p>
                    b. You shall be refunded the Membership Deposit upon expiry of the Term except for circumstances in subsection (c) below.
                </p>
                <p>
                    c. You acknowledge and agree that this Membership Deposit shall without prejudice to the Service Provider’s other rights in law, be forfeited to the Service Provider in the event:
                </p>
                <table class="table table-condensed table-borderless table-list">
                    <colgroup>
                        <col width="2%">
                        <col width="2%">
                        <col width="96%">
                    </colgroup>
                    <tr>
                        <td></td>
                        <td>
                            i)
                        </td>
                        <td>
                            of any late or non-payment of Membership Fee (as herein defined);
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            ii)
                        </td>
                        <td>
                            of theft, damage or loss of any Access Device;
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            iii)
                        </td>
                        <td>
                            usage of the Access Device is not in line with this T&C;
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            iv)
                        </td>
                        <td>
                            of damage or loss of any furniture, fixtures and fittings at the Center; or
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            v)
                        </td>
                        <td>
                            of this T&C being terminated for any reasons whatsoever not due to the fault of the Service Provider including to a breach of any warranty or representations set forth in this T&C and any material and immaterial breach of terms and conditions of the Rules & Regulations and/or House Rules as may be determined from time to time by the Service Provider.
                        </td>
                    </tr>
                </table>
                <br />
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        5. MEMBERSHIP FEES
                    </b>
                </h3>
                <br />
                <p>
                    a. During the Term of your Membership Plan, you are required to pay to the Service Provider your recurring and/or non-recurring Membership Fee stipulated in your Membership Plan and (if applicable) any additional fee, subject to any relevant GST charges (if applicable) on the 1st Business Day of each month.
                </p>
                <p>
                    b. You must conclude payment for subsection (d) not later a grace period of seven (7) Business days after the 1st Business Day of each month, failure which, your Membership Plan will be terminated.
                </p>
                <p>
                    c. All Membership Fee are non-refundable for any reason whatsoever.
                </p>
                <p>
                    d. All Fees must be paid in the currency of Ringgit Malaysia or in other currency as may be agreed by the Service Provider.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        6. TERM
                    </b>
                </h3>
                <br />

                <p>
                    This T&C will be effective starting from the Start Date and shall remain valid and legally enforceable throughout the term of your Membership Plan unless terminated in accordance to this T&C ("<b>Term</b>"). If you wish to extend your Term of your Membership Plan, you must provide at least one (1) month written notice to the Service Provider.
                </p>

            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        7. DEFAULT & TERMINATION
                    </b>
                </h3>
                <br />
                <p>
                    This T&C is an agreement and shall continue in force and effect until and unless otherwise terminated by either parties according to this T&C.
                </p>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        8. DEFAULT
                    </b>
                </h3>
                <br />

                <p>
                    Default will occur in the event of:
                </p>
                <br />
                <p>
                    a. any breach on payment of Consideration as per <b>clause 4</b> above; or
                </p>
                <p>
                    b. you failing, or if the Service Provider suspects for any reason whatsoever, that you fail to observe and comply with any of the provisions of this T&C; or
                </p>
                <p>
                    c. any reasons whatsoever not due to the fault of the Service Provider including to a breach of any warranty or representations set forth in this T&C and any material and immaterial breach of terms and conditions of the Rules & Regulations and/or House Rules as may be determined from time to time by the Service Provider; or
                </p>
                <p>
                    d. any breach of oral representations mutually agreed by the parties (if any), or
                </p>
                <p>
                    e. for any reason whatsoever attributable to you which is deemed not in the best commercial interest by the Service Provider,
                </p>
                <br />
                <p>
                    the Service Provider will then be given an option to terminate your Membership Plan, and/or restrict your access into the Center and/or discontinue your Membership and/or decline to provide any Services or Membership Benefits to you.
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        9. PARTIES’ OBLIGATIONS POST- TERMINATION/EXPIRY
                    </b>
                </h3>
                <br />

                <p>
                    Default will occur in the event of:
                </p>
                <br />
                <p>
                    a. You agree that no payment in whatsoever amount is refundable and the Service Provider may exercise their rights and obligations to collect any Fee owing; and
                </p>
                <p>
                    b. You shall return all properties of the Service Provider, including all Access Device(s) (if any); and
                </p>
                <p>
                    c. You shall remove all the Member's property, and (if any) your Affiliates' (as herein defined) property at the Center, including locker rooms, cupboards, drawers at the Center; and upon reasonable notice, the Service Provider reserves the right to dispose of any property remaining at the Center.
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        10. INDEMNITY
                    </b>
                </h3>
                <br />

                <p>
                    Without prejudice to any other rights and remedies which the Service Provider may have under this T&C and in law, you and your employers, employees, agents, independent contractors, sole proprietor, assignees, invitees, guests, affiliates; and in the event you are a Company, your parent holding company and subsidiaries of the parent company, its subsidiaries, directors, shareholders, employers, employees, agents, independent contractors, sole proprietor, assignees, affiliates ("<b>Affiliates</b>") undertake to indemnify and hold harmless COMMON GROUND on full cost basis against all and any claims, liabilities, suits, litigation, proceedings, prosecutions, fines, penalties, damages, deficiencies, losses, costs, and expenses, special, incidental, exemplary, punitive or monetary damages, loss of profits, expectation or reliance loss, which may be brought, instituted or imposed, direct and indirect regardless on the form of action, whether in contract, tort, specific performance or otherwise (collectively hereinafter “Cause of Action”) on COMMON GROUND or which may be suffered or sustained by COMMON GROUND as a result or in connection to any breach of any warranties, representations and agreements made by you herein. You undertake to indemnify and hold harmless of COMMON GROUND against all and any Causes of Action initiated from the usage of the Services including but not limited to physical damages on the property, physical injuries, food poisoning, loss or theft of intellectual property, loss or damage of data from electricity outage, door access problems, software, non-functioning or air-conditioner and non- functioning of lights. You agree to indemnify COMMON GROUND against any damage, liability, loss, cost and expenses incurred by any persons, guests or strangers who was brought into the Center by you and you shall not provide any authorization, consent, instructions, undertaking or settlement that requires a material act or admission by COMMON GROUND without prior written consent of an authorized personnel of COMMON GROUND; and you hereby indemnify and hold harmless COMMON GROUND against such authorization, consent, instruction, undertaking or settlement.
                </p>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        11. DISCLAIMER
                    </b>
                </h3>
                <br />

                <p>
                    Services are provided at a “as is where is” basis and COMMON GROUND disclaim all conditions and warranties, express and/or implied, with respect to
                </p>
                <br />
                <p>
                    a. Services, including but not limited to representations, by any means, as to the availability, accessibility, operation, performance of Services, or any other products or services accessed via the Services;
                </p>
                <p>
                    b. Commercial and non-commercial merchantability, quality, fitness, purpose, title, non-infringement, any implied terms and warranties of Services; and
                </p>
                <p>
                    c. Indemnification arising from course of dealing, course of performance or trade in connection with this T&C.
                </p>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        12. PERSONAL DATA PROTECTION ACT ("PDPA")
                    </b>
                </h3>
                <br />

                <p>
                    The Service Provider will process and may disclose personal data including sensitive personal data (as defined in PDPA) relating to you and your Affiliates, and you consent to the processing and disclosure of such data. You agree to keep the Service Provider informed of any changes to the data at all material times. In any event, should such necessity arises to obtaining consent, authorization or permission of any of your Affiliates in relation to processing and disclosure of personal data including sensitive personal data (as defined in PDPA), such consent, authorization or permission is deemed to have been obtained by you unless communicated otherwise to the Service Provider.
                </p>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        13. CONFIDENTIALITY
                    </b>
                </h3>
                <br />

                <p>
                    You shall also keep confidential and not disclose or make use of any part of the Service Provider’s Intellectual Property Rights or the Service Provider’s Know-How or any other confidential information relating to the Services provided by the Service Provider (except to the extent that the Service Provider’s Know-How or any part thereof has come into the public domain otherwise than through unauthorized disclosure by you).
                </p>
                <p>
                    "Intellectual Property Rights" means all applicable rights, title, interests and benefits thereto including, without limitation, patents, copyrights, trademarks, trade secrets, trade name, logo, patent, invention, registered and unregistered design rights, copyrights, database, database rights and all other similar intellectual property rights including, without limitation, all copies, customization, modifications, enhancements, versions, reproductions or translations of COMMON GROUND.
                </p>
                <p>
                    "Know-How" means all confidential and proprietary industrial and commercial information and techniques in any form, including but not limited to, drawings, formulae, test, results, procedures, project reports and testing procedures, instructions, training manual, market forecast, and list of particulars of potential competitors, suppliers and members.
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        14. CONTACT
                    </b>
                </h3>
                <br />

                <p>
                    The Service Provider can be contacted at Penthouse, 16-1, Level 16 Wisma UOA Damansara II No 6,, Changkat Semantan, Bukit Damansara, 50490 Kuala Lumpur, Federal Territory of Kuala Lumpur
                </p>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        15. NATURE OF T&C
                    </b>
                </h3>
                <br />
                <p>
                    Nothing in this T&C shall be construed as creating a landlord-tenant, lessor-lessee or partnership. Nothing in this T&C shall be construed as granting to you or your Affiliates any title, easement, lien, possession or related rights, tenancy interest, leasehold estate or any property interest at the Center, Space or Services and the parties agree throughout the Term and upon termination of expiration of this T&C, whichever later, the Space always remain the property of the Service Provider.
                </p>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        16. SUCCESSORS & ASSIGNS
                    </b>
                </h3>
                <br />

                <p>
                    This T&C shall be binding upon and inure for the benefit of the respective heirs, personal representatives, successors-in-title, permitted assigns or affiliates, as the case may be, of the parties but shall not be assignable by any party without the prior written consent of the other.
                </p>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        17. VARIATION
                    </b>
                </h3>
                <br />

                <p>
                    A variation of this T&C must be in writing and signed by the Service Provider or by persons authorised to sign for them.
                </p>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        18. AMENDMENTS & ADDITIONS
                    </b>
                </h3>
                <br />

                <p>
                    No amendment, variation, revocation, cancellation, substitution or waiver of, or addition or supplement to, any of the provisions of this T&C shall be effective unless it is consented in writing by the Service Provider.
                </p>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        19. INVALIDITY & SEVERABILITY
                    </b>
                </h3>
                <br />

                <p>
                    If any provision of this T&C is or may become under any written law, or is found by any court or administrative body or competent jurisdiction to be, illegal, void, invalid, prohibited or unenforceable then-
                </p>
                <br />
                <p>
                    a. such provision shall be ineffective to the extent of such illegality, voidness, invalidity, prohibition or unenforceability;
                </p>
                <p>
                    b. the remaining provisions of this T&C shall remain in full force and effect; and
                </p>
                <p>
                    c. the parties shall use their respective best endeavors to negotiate and agree a substitute provision which is valid and enforceable and achieves to the greatest extent possible of the economic, legal and commercial objectives of such illegal, void, invalid, prohibited or unenforceable term, condition, stipulation, provision, covenant or undertaking.
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        20. WAIVER
                    </b>
                </h3>
                <br />

                <p>
                    A party waives any right under this T&C only if it does so in writing. A party does not waive any right simply because it –
                </p>
                <br />
                <p>
                    a. Fails to exercise the right;
                </p>
                <p>
                    b. Delays exercising the right; or
                </p>
                <p>
                    c. Only exercises part of the right.
                </p>
                <br />
                <p>
                    A waiver of one breach of a term of this T&C does not operate as a waiver of another breach of the same term or any other term.
                </p>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        21. FURTHER ACTION
                    </b>
                </h3>
                <br />

                <p>
                    Each party must promptly sign any document and do anything else that is necessary or reasonably requested by the other party to give full effect to this T&C.
                </p>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        22. KNOWLEDGE & ACQUIESCENCE
                    </b>
                </h3>
                <br />

                <p>
                    Knowledge or acquiescence by any party of, or in, any breach of any of the provisions of this T&C shall not operate as, or be deemed to be, a waiver of such provisions and, notwithstanding such knowledge or acquiescence, such party shall remain entitled to exercise its rights and remedies under this T&C, and at law, and to require strict performance of all of the provisions of this T&C.
                </p>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>
                    <b>
                        23. LAW
                    </b>
                </h3>
                <br />

                <p>
                    This T&C is governed by, and construed in accordance with, the laws of Malaysia.
                </p>

            </div>
        </div>


    </div>

@endsection