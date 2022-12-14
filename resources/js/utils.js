import dayjs from "dayjs";
import { Inertia } from "@inertiajs/inertia";

export function logout() {
  Inertia.post(route("post.logout"));
}

export function formatName(lName, mName, fName) {
  if (mName == null || mName.length === 0) {
    return `${lName}, ${fName}`;
  }
  return `${lName}, ${fName} ${mName[0]}.`;
}

export function formatSchoolYear(schoolYear) {
  return schoolYear == null
    ? "None"
    : `${schoolYear.start} to ${schoolYear.end}`;
}

export function formatTime(time) {
  return dayjs(time).format("hh:mm A");
}

export function allowNumberOnly(input) {
  return !input || /^\d+$/.test(input);
}
