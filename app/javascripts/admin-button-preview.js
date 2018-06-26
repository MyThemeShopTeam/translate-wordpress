const init_admin_button_preview = function () {
	const $ = jQuery

	const execute = () => {
		// Init old type flags
		let old_type_flags = $("#type_flags").val()

		let destination_languages = []
		destination_languages.push($(".country-selector label").data("code-language"));
		$(".country-selector li").each((key, itm) => {
			destination_languages.push($(itm).data("code-language"));
		})

		const weglot_desination_languages = weglot_languages.available.filter(itm => {
			return destination_languages.indexOf(itm.code) >= 0;
		})

		// Change dropdown
		$("#is_dropdown").on("change", function(){
			$(".country-selector").toggleClass("weglot-inline");
            $(".country-selector").toggleClass("weglot-dropdown");
		})

		// Change with flags
		$("#with_flags").on("change", function() {
			$(".country-selector label, .country-selector li").toggleClass("weglot-flags");
		});

		// Change type flags
		$("#type_flags").on("change", function(e) {
			$(".country-selector label, .country-selector li").removeClass(`flag-${old_type_flags}`);
			const new_type_flags = e.target.value;
			$(".country-selector label, .country-selector li").addClass(`flag-${new_type_flags}`);
			old_type_flags = new_type_flags;
		});

		const set_fullname_language = () => {
			const label_language = weglot_desination_languages.find(
				(itm) => itm.code === $(".country-selector label").data("code-language")
			);

			$(".country-selector label a").text(label_language.local);

			$(".country-selector li").each((key, itm) => {
				const li_language = weglot_desination_languages.find(
					(lang) => lang.code === $(itm).data("code-language")
				);

				$(itm).find("a").text(li_language.local);
			})
		}

		// Change with name
		$("#with_name").on("change", function(e) {
			if(e.target.checked){
				set_fullname_language()
			}
			else{
				$(".country-selector label a").text("");
				$(".country-selector li a").each((key, itm) => {
					$(itm).text("");
				});
			}
		});



		$("#is_fullname").on("change", function(e){
			if (e.target.checked) {
				set_fullname_language();

			}
			else {
				const label_language = weglot_desination_languages.find(itm => itm.code === $(".country-selector label").data("code-language"));

				$(".country-selector label a").text(label_language.code.toUpperCase());
				$(".country-selector li").each((key, itm) => {
					const language = weglot_desination_languages.find(lang => lang.code === $(itm).data("code-language"));

					$(itm).find("a").text(language.code.toUpperCase());
				});
			}
		});
	}

	document.addEventListener('DOMContentLoaded', () => {
		execute();
	})
}

export default init_admin_button_preview;

